<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\Worker;

use FFMpeg\FFProbe;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaExtractor\Transmutor;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaCache\CacheIdStrategy\CacheIdStrategyInterface;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\MediaCache\Storage\StorageManager;
use Phlexible\Component\MediaTemplate\Applier\VideoTemplateApplier;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaTemplate\Model\VideoTemplate;
use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Video cache worker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VideoWorker extends AbstractWorker
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
     * @var MediaTypeManagerInterface
     */
    private $mediaTypeManager;

    /**
     * @var CacheIdStrategyInterface
     */
    private $cacheIdStrategy;

    /**
     * @var VideoTemplateApplier
     */
    private $applier;

    /**
     * @var FFProbe
     */
    private $analyzer;

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
     * @param Transmutor                $transmutor
     * @param CacheManagerInterface     $cacheManager
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param CacheIdStrategyInterface  $cacheIdStrategy
     * @param VideoTemplateApplier      $applier
     * @param FFProbe                   $analyzer
     * @param LoggerInterface           $logger
     * @param string                    $tempDir
     */
    public function __construct(
        StorageManager $storageManager,
        Transmutor $transmutor,
        CacheManagerInterface $cacheManager,
        MediaTypeManagerInterface $mediaTypeManager,
        CacheIdStrategyInterface $cacheIdStrategy,
        VideoTemplateApplier $applier,
        FFProbe $analyzer,
        LoggerInterface $logger,
        $tempDir)
    {
        $this->storageManager = $storageManager;
        $this->transmutor = $transmutor;
        $this->cacheManager = $cacheManager;
        $this->mediaTypeManager = $mediaTypeManager;
        $this->cacheIdStrategy = $cacheIdStrategy;
        $this->applier = $applier;
        $this->analyzer = $analyzer;
        $this->logger = $logger;
        $this->tempDir = $tempDir;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        return $template instanceof VideoTemplate && $mediaType->getCategory() === 'video';
    }

    /**
     * {@inheritdoc}
     */
    public function process(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        $videoFile = $this->transmutor->transmuteToVideo($file);

        return $this->work($template, $file, $videoFile);
    }

    /**
     * Apply template to filename
     *
     * @param VideoTemplate         $template
     * @param ExtendedFileInterface $file
     * @param string                $inputFilename
     *
     * @return CacheItem
     */
    private function work(VideoTemplate $template, ExtendedFileInterface $file, $inputFilename)
    {
        $volume      = $file->getVolume();
        $fileId      = $file->getID();
        $fileVersion = $file->getVersion();

        $cacheId      = $this->cacheIdStrategy->createCacheId($template, $file);
        $tempFilename = $this->tempDir . '/' . $cacheId . '.' . $template->getParameter('video_format', 'flv');

        $cacheItem = $this->cacheManager->find($cacheId);
        if (!$cacheItem) {
            $cacheItem = new CacheItem();
            $cacheItem->setId($cacheId);
        }

        $cacheItem
            ->setSiteId($volume->getId())
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
            $this->applyError($cacheItem, CacheItem::STATUS_MISSING, 'Input file not found.', $inputFilename, $template, $file);
        } elseif (!$this->applier->isAvailable($inputFilename)) {
            $this->applyError($cacheItem, CacheItem::STATUS_MISSING, 'No suitable video template applier found.', $inputFilename, $template, $file);
        } else {
            $filesystem = new Filesystem();
            if (!$filesystem->exists($this->tempDir)) {
                $filesystem->mkdir($this->tempDir, 0777);
            }
            if (!$filesystem->exists($tempFilename)) {
                $filesystem->remove($tempFilename);
            }

            try {
                $matchFormat = $template->hasParameter('match_format') ? $template->getParameter('match_format') : false;
                if ($matchFormat && strtolower($file->getMediaType()) === strtolower($template->getParameter('format'))) {
                    $tempFilename = $inputFilename;
                } else {
                    $this->applier->apply($template, $inputFilename, $tempFilename);
                }

                $videoStream = $this->analyzer->streams($tempFilename)->videos()->first();
                $width = $videoStream->getDimensions()->getWidth();
                $height = $videoStream->getDimensions()->getHeight();

                $filesystem->chmod($tempFilename, 0777);

                $mediaType = $this->mediaTypeManager->findByFilename($tempFilename);

                $cacheItem
                    ->setCacheStatus(CacheItem::STATUS_OK)
                    ->setQueueStatus(CacheItem::QUEUE_DONE)
                    ->setMimeType($mediaType->getMimeType())
                    ->setMediaType($mediaType->getKey())
                    ->setExtension(pathinfo($tempFilename, PATHINFO_EXTENSION))
                    ->setFilesize(filesize($tempFilename))
                    ->setWidth($width)
                    ->setHeight($height)
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
