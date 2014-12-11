<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Worker;

use Phlexible\Bundle\MediaCacheBundle\CacheIdStrategy\CacheIdStrategyInterface;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaCacheBundle\Model\CacheManagerInterface;
use Phlexible\Bundle\MediaCacheBundle\Storage\StorageManager;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Applier\PdfTemplateApplier;
use Phlexible\Component\MediaTemplate\Model\PdfTemplate;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
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
     * @var MediaTypeManagerInterface
     */
    private $mediaTypeManager;

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
     * @param StorageManager            $storageManager
     * @param CacheManagerInterface     $cacheManager
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param CacheIdStrategyInterface  $cacheIdStrategy
     * @param PdfTemplateApplier        $applier
     * @param LoggerInterface           $logger
     * @param string                    $tempDir
     */
    public function __construct(
        StorageManager $storageManager,
        CacheManagerInterface $cacheManager,
        MediaTypeManagerInterface $mediaTypeManager,
        CacheIdStrategyInterface $cacheIdStrategy,
        PdfTemplateApplier $applier,
        LoggerInterface $logger,
        $tempDir)
    {
        $this->storageManager = $storageManager;
        $this->cacheManager = $cacheManager;
        $this->mediaTypeManager = $mediaTypeManager;
        $this->cacheIdStrategy = $cacheIdStrategy;
        $this->applier = $applier;
        $this->logger = $logger;
        $this->tempDir = $tempDir;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        return $template instanceof PdfTemplate && $mediaType->getName() === 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function process(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        return $this->work($template, $file, $file->getPhysicalPath());
    }

    /**
     * Apply template to filename
     *
     * @param PdfTemplate           $template
     * @param ExtendedFileInterface $file
     * @param string                $inputFilename
     *
     * @return CacheItem
     */
    private function work(PdfTemplate $template, ExtendedFileInterface $file, $inputFilename)
    {
        $volume = $file->getVolume();
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
            ->setVolumeId($volume->getId())
            ->setFileId($fileId)
            ->setFileVersion($fileVersion)
            ->setTemplateKey($template->getKey())
            ->setTemplateRevision($template->getRevision())
            ->setCacheStatus(CacheItem::STATUS_DELEGATE)
            ->setQueueStatus(CacheItem::QUEUE_DONE)
            ->setMimeType($file->getMimeType())
            ->setMediaType(strtolower($file->getMediaType()))
            ->setExtension('')
            ->setFileSize(0)
            ->setError(null);

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

                $mediaType = $this->mediaTypeManager->findByFilename($tempFilename);

                $cacheItem
                    ->setCacheStatus(CacheItem::STATUS_OK)
                    ->setQueueStatus(CacheItem::QUEUE_DONE)
                    ->setMimeType($mediaType->getMimeType())
                    ->setMediaType($mediaType->getName())
                    ->setExtension(pathinfo($tempFilename, PATHINFO_EXTENSION))
                    ->setFilesize(filesize($tempFilename))
                    ->setFinishedAt(new \DateTime());
            } catch (\Exception $e) {
                $cacheItem
                    ->setCacheStatus(CacheItem::STATUS_ERROR)
                    ->setQueueStatus(CacheItem::QUEUE_ERROR)
                    ->setError($e)
                    ->setFinishedAt(new \DateTime());
            }

            if ($cacheItem->getCacheStatus() === CacheItem::STATUS_OK) {
                $storage = $this->storageManager->get($template->getStorage());
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
