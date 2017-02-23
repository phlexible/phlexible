<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Queue;

use Phlexible\Bundle\GuiBundle\Properties\Properties;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaCacheBundle\Exception\AlreadyRunningException;
use Phlexible\Component\MediaCache\Worker\WorkerResolver;
use Phlexible\Component\MediaTemplate\Model\TemplateManagerInterface;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Phlexible\Component\Volume\VolumeManager;
use Symfony\Component\Filesystem\LockHandler;

/**
 * Queue processor.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class QueueProcessor
{
    /**
     * @var WorkerResolver
     */
    private $workerResolver;

    /**
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var MediaTypeManagerInterface
     */
    private $mediaTypeManager;

    /**
     * @var Properties
     */
    private $properties;

    /**
     * @var string
     */
    private $lockDir;

    /**
     * @param WorkerResolver            $workerResolver
     * @param VolumeManager             $volumeManager
     * @param TemplateManagerInterface  $templateManager
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param Properties                $properties
     * @param string                    $lockDir
     */
    public function __construct(
        WorkerResolver $workerResolver,
        VolumeManager $volumeManager,
        TemplateManagerInterface $templateManager,
        MediaTypeManagerInterface $mediaTypeManager,
        Properties $properties,
        $lockDir)
    {
        $this->workerResolver = $workerResolver;
        $this->volumeManager = $volumeManager;
        $this->templateManager = $templateManager;
        $this->mediaTypeManager = $mediaTypeManager;
        $this->properties = $properties;
        $this->lockDir = $lockDir;
    }

    /**
     * @param Queue    $queue
     * @param callable $callback
     *
     * @return CacheItem
     */
    public function processQueue(Queue $queue, callable $callback = null)
    {
        $lock = $this->lock();
        foreach ($queue->all() as $cacheItem) {
            $this->doProcess($cacheItem, $callback);
        }
        $lock->release();
    }

    /**
     * @param CacheItem $cacheItem
     * @param callable  $callback
     *
     * @return CacheItem
     */
    public function processItem(CacheItem $cacheItem, callable $callback = null)
    {
        $lock = $this->lock();
        $cacheItem = $this->doProcess($cacheItem, $callback);
        $lock->release();

        return $cacheItem;
    }

    /**
     * @return LockHandler
     *
     * @throws AlreadyRunningException
     */
    private function lock()
    {
        $lock = new LockHandler('mediacache_lock', $this->lockDir);
        if (!$lock->lock(false)) {
            throw new AlreadyRunningException('Another cache worker process running.');
        }

        return $lock;
    }

    /**
     * @param CacheItem $cacheItem
     * @param callable  $callback
     *
     * @return CacheItem
     */
    private function doProcess(CacheItem $cacheItem, callable $callback = null)
    {
        $volume = $this->volumeManager->getById($cacheItem->getVolumeId());
        $file = $volume->findFile($cacheItem->getFileId(), $cacheItem->getFileVersion());

        if (!$file) {
            return null;
        }

        $template = $this->templateManager->find($cacheItem->getTemplateKey());

        $mediaType = $this->mediaTypeManager->find($file->getMediaType());

        $worker = $this->workerResolver->resolve($template, $file, $mediaType);
        if (!$worker) {
            if ($callback && $cacheItem) {
                call_user_func($callback, 'no_worker', null, $cacheItem);
            }

            return null;
        }

        $cacheItem = $worker->process($cacheItem, $template, $file, $mediaType);

        if ($callback) {
            if (!$cacheItem) {
                call_user_func($callback, 'no_cacheitem', $worker, null);
            } else {
                call_user_func($callback, $cacheItem->getCacheStatus(), $worker, $cacheItem);
            }
        }

        $this->properties->set('mediacache', 'last_run', date('Y-m-d H:i:s'));

        return $cacheItem;
    }
}
