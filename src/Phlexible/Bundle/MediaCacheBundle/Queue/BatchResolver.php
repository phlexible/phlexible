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
use Phlexible\Bundle\MediaCacheBundle\Model\CacheManagerInterface;

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
     * @var CacheIdStrategyInterface
     */
    private $cacheIdStrategy;

    /**
     * @param CacheManagerInterface    $cacheManager
     * @param CacheIdStrategyInterface $cacheIdStrategy
     */
    public function __construct(CacheManagerInterface $cacheManager, CacheIdStrategyInterface $cacheIdStrategy)
    {
        $this->cacheManager = $cacheManager;
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

                $cacheItem = $this->cacheManager->findByTemplateAndFile($template->getKey(), $fileId, $fileVersion);

                if (count($flags)) {

                    if (isset($flags['error']) && !$this->isError($cacheItem)) {
                        continue;
                    }

                    if (isset($flags['missing']) && !$this->isMissing($cacheItem)) {
                        continue;
                    }

                    if (isset($flags['uncached']) && $this->isCached($cacheItem)) {
                        continue;
                    }
                }


                if (!$cacheItem) {
                    $cacheItem = new CacheItem();
                    $cacheItem
                        ->setId($this->cacheIdStrategy->createCacheId($template, $file))
                        ->setSiteId($file->getSite()->getId())
                        ->setFileId($file->getId())
                        ->setTemplateKey($template->getKey())
                        ->setCreatedAt(new \DateTime())
                        ->setFileVersion($file->getVersion());
                }

                $cacheItem
                    ->setQueuedAt(new \DateTime());

                $queue->add($cacheItem);
            }
        }

        return $queue;
    }

    /**
     * @param CacheItem $cacheItem
     *
     * @return bool
     */
    private function isCached(CacheItem $cacheItem)
    {
        return $this->isWaiting($cacheItem)
            || $this->isExisting($cacheItem);
    }

    /**
     * @param CacheItem $cacheItem
     *
     * @return bool
     */
    private function isError(CacheItem $cacheItem)
    {
        return $cacheItem->getCacheStatus() === CacheItem::STATUS_ERROR || $cacheItem->getQueueStatus() === CacheItem::QUEUE_ERROR;
    }

    /**
     * @param CacheItem $cacheItem
     *
     * @return bool
     */
    private function isMissing(CacheItem $cacheItem)
    {
        return $cacheItem->getCacheStatus() === CacheItem::STATUS_MISSING;
    }

    /**
     * @param CacheItem $cacheItem
     *
     * @return bool
     */
    private function isExisting(CacheItem $cacheItem)
    {
        return (bool) $cacheItem;
    }

    /**
     * @param CacheItem $queueItem
     *
     * @return bool
     */
    private function isWaiting(CacheItem $queueItem)
    {
        return $queueItem->getQueueStatus() === CacheItem::QUEUE_WAITING;
    }
}
