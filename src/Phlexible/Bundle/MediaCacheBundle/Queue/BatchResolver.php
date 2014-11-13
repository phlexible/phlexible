<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Queue;

use Phlexible\Bundle\MediaCacheBundle\CacheIdStrategy\CacheIdStrategyInterface;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaCacheBundle\Entity\QueueItem;
use Phlexible\Bundle\MediaCacheBundle\Model\CacheManagerInterface;
use Phlexible\Bundle\MediaCacheBundle\Model\QueueManagerInterface;

/**
 * Resolves batch into queue
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BatchResolver
{
    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var QueueManagerInterface
     */
    private $queueManager;

    /**
     * @var CacheIdStrategyInterface
     */
    private $cacheIdStrategy;

    /**
     * @param CacheManagerInterface    $cacheManager
     * @param QueueManagerInterface    $queueManager
     * @param CacheIdStrategyInterface $cacheIdStrategy
     */
    public function __construct(CacheManagerInterface $cacheManager,
                                QueueManagerInterface $queueManager,
                                CacheIdStrategyInterface $cacheIdStrategy)
    {
        $this->cacheManager = $cacheManager;
        $this->queueManager = $queueManager;
        $this->cacheIdStrategy = $cacheIdStrategy;
    }

    /**
     * Resolve batch to queue items
     *
     * @param Batch $batch
     * @param array $flags
     *
     * @return Queue
     */
    public function resolve(Batch $batch, array $flags = [])
    {
        $queue = new Queue();

        foreach ($batch->getFiles() as $file) {
            $fileId = $file->getId();
            $fileVersion = $file->getVersion();

            foreach ($batch->getTemplates() as $template) {
                if (!$template->getCache()) {
                    continue;
                }

                if (count($flags)) {
                    $cacheItem = $this->cacheManager->findByTemplateAndFile($template->getKey(), $fileId, $fileVersion);

                    if (isset($flags['error']) && !$this->isCacheStatusError($cacheItem)) {
                        continue;
                    }

                    if (isset($flags['missing']) && !$this->isCacheStatusMissing($cacheItem)) {
                        continue;
                    }

                    if (isset($flags['uncached'])) {
                        $queueItem = $this->queueManager->findByTemplateAndFile($template->getKey(), $fileId, $fileVersion);

                        if ($this->isItemCached($cacheItem, $queueItem)) {
                            continue;
                        }
                    }
                }

                $id = $this->cacheIdStrategy->createCacheId($template, $file);

                $queueItem = $this->queueManager->find($id);
                if (!$queueItem) {
                    $queueItem = new QueueItem();
                    $queueItem->setId($id);
                }

                $queueItem
                    ->setSiteId($file->getSite()->getId())
                    ->setFileId($file->getId())
                    ->setFileVersion($file->getVersion())
                    ->setTemplateKey($template->getKey())
                    ->setCreatedAt(new \DateTime());

                $queue->add($queueItem);
            }
        }

        return $queue;
    }

    /**
     * @param CacheItem $cacheItem
     * @param QueueItem $queueItem
     *
     * @return bool
     */
    private function isItemCached(CacheItem $cacheItem, QueueItem $queueItem)
    {
        return $this->isCacheItemWaiting($queueItem)
            || $this->isCacheItemExisting($cacheItem);
    }

    /**
     * @param CacheItem $cacheItem
     *
     * @return bool
     */
    private function isCacheStatusError(CacheItem $cacheItem)
    {
        return $cacheItem->getStatus() === CacheItem::STATUS_ERROR;
    }

    /**
     * @param CacheItem $cacheItem
     *
     * @return bool
     */
    private function isCacheStatusMissing(CacheItem $cacheItem)
    {
        return $cacheItem->getStatus() === CacheItem::STATUS_MISSING;
    }

    /**
     * @param CacheItem $cacheItem
     *
     * @return bool
     */
    private function isCacheItemExisting(CacheItem $cacheItem)
    {
        return (bool) $cacheItem;
    }

    /**
     * @param QueueItem $queueItem
     *
     * @return bool
     */
    private function isCacheItemWaiting(QueueItem $queueItem)
    {
        return (bool) $queueItem;
    }
}
