<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Worker;

use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeManagerInterface;
use Phlexible\Bundle\MediaAssetBundle\Transmutor;
use Phlexible\Bundle\MediaCacheBundle\CacheIdStrategy\CacheIdStrategyInterface;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaCacheBundle\Model\CacheManagerInterface;
use Phlexible\Bundle\MediaCacheBundle\Storage\StorageManager;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaTemplateBundle\Applier\ImageTemplateApplier;
use Phlexible\Bundle\MediaTemplateBundle\Model\ImageTemplate;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Image cache worker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImageWorker extends AbstractWorker
{
    /**
     * @var StorageManager
     */
    private $storageManager;

    /**
     * @var Transmutor
     */
    private $transmutor;

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
     * @var ImageTemplateApplier
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
     * @param Transmutor                   $transmutor
     * @param CacheManagerInterface        $cacheManager
     * @param DocumenttypeManagerInterface $documenttypeManager
     * @param CacheIdStrategyInterface     $cacheIdStrategy
     * @param ImageTemplateApplier         $applier
     * @param LoggerInterface              $logger
     * @param string                       $tempDir
     */
    public function __construct(
        StorageManager $storageManager,
        Transmutor $transmutor,
        CacheManagerInterface $cacheManager,
        DocumenttypeManagerInterface $documenttypeManager,
        CacheIdStrategyInterface $cacheIdStrategy,
        ImageTemplateApplier $applier,
        LoggerInterface $logger,
        $tempDir)
    {
        $this->storageManager = $storageManager;
        $this->transmutor = $transmutor;
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
        return $template instanceof ImageTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function process(TemplateInterface $template, FileInterface $file)
    {
        $imageFile = $this->transmutor->transmuteToImage($file);

        if ($imageFile !== null && file_exists($imageFile)) {
            // we have a preview image from the asset
            return $this->work($template, $file, $imageFile);
        } elseif (!file_exists($file->getPhysicalPath())) {
            // file is completely missing
            return $this->work($template, $file, $file->getPhysicalPath(), true);
        }

        return null;
    }

    /**
     * Apply template to filename
     *
     * @param ImageTemplate $template
     * @param FileInterface $file
     * @param string        $inputFilename
     * @param bool          $missing
     *
     * @return CacheItem
     */
    private function work(ImageTemplate $template, FileInterface $file, $inputFilename = null, $missing = false)
    {
        $cacheFilename = null;

        $site = $file->getSite();
        $fileId = $file->getId();
        $fileVersion = $file->getVersion();

        $cacheId = $this->cacheIdStrategy->createCacheId($template, $file);
        $tempFilename = $this->tempDir . '/' . $cacheId . '.' . $template->getParameter('format');

        $pathinfo = pathinfo($file->getPhysicalPath());

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
            ->setExtension(isset($pathinfo['extension']) ? $pathinfo['extension'] : '')
            ->setFileSize(0)
            ->setError(null)
            ->setCreatedAt(new \DateTime());

        if ($missing) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'Input file not found.',
                $inputFilename,
                $template,
                $file
            );
        } elseif ($inputFilename === null) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'No preview image.',
                $inputFilename,
                $template,
                $file
            );
        } elseif (!$this->applier->isAvailable($inputFilename)) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'No suitable image template applier found.',
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
                $result = $this->applier->apply($template, $file, $inputFilename, $tempFilename);

                $filesystem->chmod($tempFilename, 0777);

                $fileInfo = $this->documenttypeManager->getMimeDetector()->detect($tempFilename);
                $documentType = $this->documenttypeManager->findByMimetype($fileInfo->getMimeType());

                $cacheItem
                    ->setStatus(CacheItem::STATUS_OK)
                    ->setMimeType($fileInfo->getMimeType())
                    ->setDocumentTypeKey($documentType->getKey())
                    ->setExtension($fileInfo->getExtension())
                    ->setFilesize($fileInfo->getSize())
                    ->setWidth($result->getWidth())
                    ->setHeight($result->getHeight());
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
