<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Worker;

use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeManagerInterface;
use Phlexible\Bundle\MediaCacheBundle\CacheIdStrategy\CacheIdStrategyInterface;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaCacheBundle\Model\CacheManagerInterface;
use Phlexible\Bundle\MediaCacheBundle\Storage\StorageManager;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaTemplateBundle\Applier\PdfTemplateApplier;
use Phlexible\Bundle\MediaTemplateBundle\Model\PdfTemplate;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Pdf cache worker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PdfWorker extends AbstractWorker
{
    /**
     * @var StorageManager
     */
    private $storageManager;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var DocumenttypeManagerInterface
     */
    private $documenttypeManager;

    /**
     * @var CacheIdStrategyInterface
     */
    private $cacheIdStrategy;

    /**
     * @var PdfTemplateApplier
     */
    private $applier;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $tempDir;

    /**
     * @param StorageManager               $storageManager
     * @param CacheManagerInterface        $cacheManager
     * @param DocumenttypeManagerInterface $documenttypeManager
     * @param CacheIdStrategyInterface     $cacheIdStrategy
     * @param PdfTemplateApplier           $applier
     * @param LoggerInterface              $logger
     * @param string                       $tempDir
     */
    public function __construct(
        StorageManager $storageManager,
        CacheManagerInterface $cacheManager,
        DocumenttypeManagerInterface $documenttypeManager,
        CacheIdStrategyInterface $cacheIdStrategy,
        PdfTemplateApplier $applier,
        LoggerInterface $logger,
        $tempDir)
    {
        $this->storageManager = $storageManager;
        $this->cacheManager = $cacheManager;
        $this->documenttypeManager = $documenttypeManager;
        $this->cacheIdStrategy = $cacheIdStrategy;
        $this->applier = $applier;
        $this->logger = $logger;
        $this->tempDir = $tempDir;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template, FileInterface $file)
    {
        return $template instanceof PdfTemplate && strtolower($file->getAttribute('documenttype')) === 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function process(TemplateInterface $template, FileInterface $file)
    {
        return $this->work($template, $file, $file->getPhysicalPath());
    }

    /**
     * Apply template to filename
     *
     * @param PdfTemplate   $template
     * @param FileInterface $file
     * @param string        $inputFilename
     *
     * @return CacheItem
     */
    private function work(PdfTemplate $template, FileInterface $file, $inputFilename)
    {
        $site = $file->getSite();
        $fileId = $file->getId();
        $fileVersion = $file->getVersion();

        $cacheId = $this->cacheIdStrategy->createCacheId($template, $file);
        $tempFilename = $this->tempDir . '/' . $cacheId . '.swf';

        $cacheItem = $this->cacheManager->find($cacheId);
        if (!$cacheItem) {
            $cacheItem = new CacheItem();
            $cacheItem->setId($cacheId);
        }

        $cacheItem
            ->setSiteId($site->getId())
            ->setFileId($fileId)
            ->setFileVersion($fileVersion)
            ->setTemplateKey($template->getKey())
            ->setTemplateRevision($template->getRevision())
            ->setStatus(CacheItem::STATUS_DELEGATE)
            ->setMimeType($file->getMimeType())
            ->setDocumentTypeKey(strtolower($file->getAttribute('documenttype')))
            ->setExtension('')
            ->setFileSize(0)
            ->setError(null)
            ->setCreatedAt(new \DateTime());

        if (!file_exists($inputFilename)) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'Input file not found.',
                $inputFilename,
                $template,
                $file
            );
        } elseif (!$this->applier->isAvailable($inputFilename)) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'No suitable pdf template applier found.',
                $inputFilename,
                $template,
                $file
            );
        } else {
            $filesystem = new Filesystem();
            if (!$filesystem->exists($this->tempDir)) {
                $filesystem->mkdir($this->tempDir, 0777);
            }
            if (!$filesystem->exists($tempFilename)) {
                $filesystem->remove($tempFilename);
            }

            try {
                $this->applier->apply($template, $inputFilename, $tempFilename);

                /*
                $swfInfo = $this->swfDump->getInfo($tempFilename);

                $this->swfCombine
                    ->setMaster($this->swfViewer)
                    ->setOutput($tempFilename)
                    ->combine('viewport', $tempFilename)
                    ->write();

                $this->swfCombine
                    ->setMaster($this->swfViewer)
                    ->setDummy()
                    ->setOutput($tempFilename)
                    ->setMoveX($swfInfo->width)
                    ->setMoveY($swfInfo->height)
                    ->write();
                */

                $filesystem->chmod($tempFilename, 0777);

                $fileInfo = $this->documenttypeManager->getMimeDetector()->detect($tempFilename);
                $documentType = $this->documenttypeManager->findByMimetype($fileInfo->getMimeType());

                $cacheItem
                    ->setStatus(CacheItem::STATUS_OK)
                    ->setMimeType($fileInfo->getMimeType())
                    ->setDocumentTypeKey($documentType->getKey())
                    ->setExtension($fileInfo->getExtension())
                    ->setFilesize($fileInfo->getSize());
            } catch (\Exception $e) {
                $cacheItem
                    ->setStatus(CacheItem::STATUS_ERROR)
                    ->setError($e);
            }

            if ($cacheItem->getStatus() === CacheItem::STATUS_OK) {
                $storage = $this->storageManager->getStorage($template->getStorage());
                $storage->store($cacheItem, $tempFilename);
            }
        }

        $this->cacheManager->updateCacheItem($cacheItem);

        if ($cacheItem->getError()) {
            $this->logger->error($cacheItem->getError());
        }

        return $cacheItem;
    }
}
