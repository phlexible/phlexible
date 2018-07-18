<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Worker;

use Phlexible\Component\MediaCache\Domain\CacheItem;
use Phlexible\Component\MediaCache\CacheIdStrategy\CacheIdStrategyInterface;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\MediaCache\Storage\StorageManager;
use Phlexible\Component\MediaExtractor\Transmutor;
use Phlexible\Component\MediaTemplate\Applier\AudioTemplateApplier;
use Phlexible\Component\MediaTemplate\Model\AudioTemplate;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Audio cache worker.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AudioWorker implements WorkerInterface
{
    use WorkerLogger;

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
     * @param AudioTemplateApplier      $applier
     * @param LoggerInterface           $logger
     * @param string                    $tempDir
     */
    public function __construct(
        StorageManager $storageManager,
        Transmutor $transmutor,
        CacheManagerInterface $cacheManager,
        MediaTypeManagerInterface $mediaTypeManager,
        AudioTemplateApplier $applier,
        LoggerInterface $logger,
        $tempDir
    ) {
        $this->storageManager = $storageManager;
        $this->transmutor = $transmutor;
        $this->cacheManager = $cacheManager;
        $this->mediaTypeManager = $mediaTypeManager;
        $this->applier = $applier;
        $this->logger = $logger;
        $this->tempDir = $tempDir;
    }

    /**
     * {@inheritdoc}
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template, InputDescriptor $input, MediaType $mediaType)
    {
        return $template instanceof AudioTemplate && $mediaType->getCategory() === 'audio';
    }

    /**
     * {@inheritdoc}
     */
    public function process(CacheItem $cacheItem, TemplateInterface $template, InputDescriptor $input, MediaType $mediaType)
    {
        $audioFile = $this->transmutor->transmuteToAudio($input);

        $cacheItem
            ->setVolumeId($input->getVolumeId())
            ->setFileId($input->getFileId())
            ->setFileVersion($input->getFileVersion())
            ->setMimeType($input->getMimeType())
            ->setMediaType($input->getMediaType());

        $this->work($cacheItem, $template, $audioFile);
    }

    /**
     * Apply template to filename.
     *
     * @param CacheItem     $cacheItem
     * @param AudioTemplate $template
     * @param string        $inputFilename
     */
    private function work(CacheItem $cacheItem, AudioTemplate $template, $inputFilename)
    {
        $tempFilename = $this->tempDir.'/'.$cacheItem->getId().'.'.$template->getParameter('audio_format');

        $cacheItem
            ->setTemplateKey($template->getKey())
            ->setTemplateRevision($template->getRevision())
            ->setCacheStatus(CacheItem::STATUS_DELEGATE)
            ->setQueueStatus(CacheItem::QUEUE_DONE)
            ->setExtension('')
            ->setFileSize(0)
            ->setError(null);

        if (!file_exists($inputFilename)) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'Input file not found.',
                $inputFilename,
                $template->getType(),
                $template->getKey()
            );
        } elseif ($this->applier->isAvailable($inputFilename)) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'No suitable audio template applier found.',
                $inputFilename,
                $template->getType(),
                $template->getKey()
            );
        } else {
            $filesystem = new Filesystem();
            if (!$filesystem->exists($this->tempDir)) {
                $filesystem->mkdir($this->tempDir, 0777);
            }
            if ($filesystem->exists($tempFilename)) {
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
                    ->setFileSize(filesize($tempFilename))
                    ->setFinishedAt(new \DateTime());
            } catch (\Exception $e) {
                $this->logger->error('Audio worker error', array('exception' => $e, 'template' => $template->getKey(), 'file' => $cacheItem->getFileId()));

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
    }
}
