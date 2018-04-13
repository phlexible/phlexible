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
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaType\Model\MediaType;
use Psr\Log\LoggerInterface;

/**
 * Null cache worker.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NullWorker implements WorkerInterface
{
    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CacheManagerInterface    $cacheManager
     * @param LoggerInterface          $logger
     */
    public function __construct(
        CacheManagerInterface $cacheManager,
        LoggerInterface $logger
    ) {
        $this->cacheManager = $cacheManager;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(TemplateInterface $template, InputDescriptor $input, MediaType $mediaType)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CacheItem $cacheItem, TemplateInterface $template, InputDescriptor $input, MediaType $mediaType)
    {
        if ($cacheItem->getCacheStatus() !== CacheItem::STATUS_OK && $cacheItem->getCacheStatus() !== CacheItem::STATUS_DELEGATE) {
            $this->cacheManager->deleteCacheItem($cacheItem);
            $cacheItem->setCacheStatus(CacheItem::STATUS_DELETED);
        }
        $cacheItem->setQueueStatus(CacheItem::QUEUE_NOT_APPLICABLE);
    }
}
