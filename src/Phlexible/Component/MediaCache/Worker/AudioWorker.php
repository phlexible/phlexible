<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\Worker;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaExtractor\Transmutor;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaCache\CacheIdStrategy\CacheIdStrategyInterface;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\MediaCache\Storage\StorageManager;
use Phlexible\Component\MediaTemplate\Applier\AudioTemplateApplier;
use Phlexible\Component\MediaTemplate\Model\AudioTemplate;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Audio cache worker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AudioWorker extends AbstractWorker
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
     * @var CacheIdStrategyInterface
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
     * @param Transmutor                $transmutor
     * @param CacheManagerInterface     $cacheManager
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param CacheIdStrategyInterface  $cacheIdStrategy
     * @param AudioTemplateApplier      $applier
     * @param LoggerInterface           $logger
     * @param string                    $tempDir
     */
    public function __construct(
        StorageManager $storageManager,
        Transmutor $transmutor,
        CacheManagerInterface $cacheManager,
        MediaTypeManagerInterface $mediaTypeManager,
        CacheIdStrategyInterface $cacheIdStrategy,
        AudioTemplateApplier $applier,
        LoggerInterface $logger,
        $tempDir)
    {
        $this->storageManager = $storageManager;
        $this->transmutor = $transmutor;
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
        return $template instanceof AudioTemplate && $mediaType->getCategory() === 'audio';
    }

    /**
     * {@inheritdoc}
     */
    public function process(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        $audioFile = $this->transmutor->transmuteToAudio($file);

        return $this->work($template, $file, $audioFile);
    }

    /**
     * Apply template to filename
     *
     * @param AudioTemplate         $template
     * @param ExtendedFileInterface $file
     * @param string                $inputFilename
     *
     * @return CacheItem
     */
    private function work(AudioTemplate $template, ExtendedFileInterface $file, $inputFilename)
    {
        $volume = $file->getVolume();
        $fileId = $file->getId();
        $fileVersion = $file->getVersion();

        $cacheId = $this->cacheIdStrategy->createCacheId($template, $file);
        $tempFilename = $this->tempDir . '/' . $cacheId . '.' . $template->getParameter('audio_format');

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
        } elseif ($this->applier->isAvailable($inputFilename)) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'No suitable audio template applier found.',
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

                $filesystem->chmod($tempFilename, 0777);

                $mediaType = $this->mediaTypeManager->findByFilename($tempFilename);

                $cacheItem
                    ->setCacheStatus(CacheItem::STATUS_OK)
                    ->setQueueStatus(CacheItem::QUEUE_DONE)
                    ->setMimeType($mediaType->getMimetype())
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
