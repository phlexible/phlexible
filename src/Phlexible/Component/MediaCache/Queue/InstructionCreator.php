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

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaCache\CacheIdStrategy\CacheIdStrategyInterface;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Psr\Log\LoggerInterface;

/**
 * Resolves batch into queue.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class InstructionCreator
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CacheManagerInterface    $cacheManager
     * @param CacheIdStrategyInterface $cacheIdStrategy
     * @param LoggerInterface          $logger
     */
    public function __construct(CacheManagerInterface $cacheManager, CacheIdStrategyInterface $cacheIdStrategy, LoggerInterface $logger)
    {
        $this->cacheManager = $cacheManager;
        $this->cacheIdStrategy = $cacheIdStrategy;
        $this->logger = $logger;
    }

    /**
     * Resolve batch to queue items.
     *
     * @param ExtendedFileInterface $file
     * @param TemplateInterface     $template
     * @param array                 $flags
     *
     * @return Instruction|null
     */
    public function createInstruction(ExtendedFileInterface $file, TemplateInterface $template, array $flags = [])
    {
        if (!$template->getManaged()) {
            return null;
        }

        $cacheItem = $this->cacheManager->findByTemplateAndFile($template->getKey(), $file->getId(), $file->getVersion());

        if (in_array(Batch::FILTER_ERROR, $flags) && $cacheItem && !$this->isError($cacheItem)) {
            $this->logger->info('Skipping non-error item');

            return null;
        }

        if (in_array(Batch::FILTER_MISSING, $flags) && $cacheItem && !$this->isMissing($cacheItem)) {
            $this->logger->info('Skipping non-missing item');

            return null;
        }

        if (in_array(Batch::FILTER_UNCACHED, $flags) && $cacheItem && $this->isCached($cacheItem)) {
            $this->logger->info('Skipping cached item');

            return null;
        }

        if (!$cacheItem) {
            $cacheItem = new CacheItem();
            $cacheItem
                ->setId($this->cacheIdStrategy->createCacheId($template, $file))
                ->setVolumeId($file->getVolume()->getId())
                ->setFileId($file->getId())
                ->setFileVersion($file->getVersion())
                ->setTemplateKey($template->getKey())
                ->setTemplateRevision($template->getRevision())
                ->setCacheStatus(CacheItem::STATUS_WAITING)
                ->setQueueStatus(CacheItem::QUEUE_WAITING)
                ->setCreatedAt(new \DateTime());
        }

        $cacheItem
            ->setQueuedAt(new \DateTime());

        return new Instruction($file, $template, $cacheItem);
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
