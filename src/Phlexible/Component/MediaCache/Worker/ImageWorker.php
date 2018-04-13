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

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\MediaCache\Storage\StorageManager;
use Phlexible\Component\MediaExtractor\Transmutor;
use Phlexible\Component\MediaTemplate\Applier\ImageTemplateApplier;
use Phlexible\Component\MediaTemplate\Model\ImageTemplate;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Image cache worker.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImageWorker implements WorkerInterface
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
     * @param StorageManager            $storageManager
     * @param Transmutor                $transmutor
     * @param CacheManagerInterface     $cacheManager
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param ImageTemplateApplier      $applier
     * @param LoggerInterface           $logger
     * @param string                    $tempDir
     */
    public function __construct(
        StorageManager $storageManager,
        Transmutor $transmutor,
        CacheManagerInterface $cacheManager,
        MediaTypeManagerInterface $mediaTypeManager,
        ImageTemplateApplier $applier,
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
        return $template instanceof ImageTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CacheItem $cacheItem, TemplateInterface $template, InputDescriptor $input, MediaType $mediaType)
    {
        $imageFile = $this->transmutor->transmuteToImage($input);

        $attributes = $input->getFileAttributes();
        $pathinfo = pathinfo($input->getFilePath());

        $cacheItem
            ->setVolumeId($input->getVolumeId())
            ->setFileId($input->getFileId())
            ->setFileVersion($input->getFileVersion())
            ->setMimeType($input->getMimeType())
            ->setMediaType($input->getMediaType())
            ->setExtension(isset($pathinfo['extension']) ? $pathinfo['extension'] : '');

        if ($imageFile !== null && file_exists($imageFile)) {
            // we have a preview image from the asset
            $this->work($cacheItem, $template, $attributes, $imageFile);
        } elseif (!file_exists($input->getFilePath())) {
            // file is completely missing
            $this->work($cacheItem, $template, $attributes, $input->getFilePath(), true);
        } elseif ($imageFile === null) {
            $this->work($cacheItem, $template, $attributes);
        }
    }

    /**
     * Apply template to filename.
     *
     * @param CacheItem     $cacheItem
     * @param ImageTemplate $template
     * @param array         $attributes
     * @param string        $inputFilename
     * @param bool          $missing
     */
    private function work(CacheItem $cacheItem, ImageTemplate $template, array $attributes, $inputFilename = null, $missing = false)
    {
        $cacheFilename = null;

        $tempFilename = $this->tempDir.'/'.$cacheItem->getId().'.'.$template->getParameter('format');

        $cacheItem
            ->setTemplateKey($template->getKey())
            ->setTemplateRevision($template->getRevision())
            ->setCacheStatus(CacheItem::STATUS_DELEGATE)
            ->setQueueStatus(CacheItem::QUEUE_DONE)
            ->setFileSize(0)
            ->setError(null);

        if ($missing) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'Input file not found.',
                $inputFilename,
                $template->getType(),
                $template->getKey()
            );
        } elseif ($inputFilename === null) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_DELEGATE,
                'No preview image.',
                $inputFilename,
                $template->getType(),
                $template->getKey(),
                'warning'
            );
        } elseif (!$this->applier->isAvailable($inputFilename)) {
            $this->applyError(
                $cacheItem,
                CacheItem::STATUS_MISSING,
                'No suitable image template applier found.',
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
                $image = $this->applier->apply($template, $attributes, $inputFilename, $tempFilename);

                $filesystem->chmod($tempFilename, 0777);

                $mediaType = $this->mediaTypeManager->findByFilename($tempFilename);

                $cacheItem
                    ->setCacheStatus(CacheItem::STATUS_OK)
                    ->setQueueStatus(CacheItem::QUEUE_DONE)
                    ->setMimeType($mediaType->getMimetype())
                    ->setMediaType($mediaType->getName())
                    ->setExtension(pathinfo($tempFilename, PATHINFO_EXTENSION))
                    ->setFileSize(filesize($tempFilename))
                    ->setWidth($image->getSize()->getWidth())
                    ->setHeight($image->getSize()->getHeight())
                    ->setFinishedAt(new \DateTime());
            } catch (\Exception $e) {
                $this->logger->error('Image worker error', array('exception' => $e, 'template' => $template->getId(), 'file' => $cacheItem->getFileId()));

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
