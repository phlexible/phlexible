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
use Phlexible\Component\MediaCache\CacheIdStrategy\CacheIdStrategyInterface;
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
class NullWorker extends AbstractWorker
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
    public function __construct(
        CacheManagerInterface $cacheManager,
        CacheIdStrategyInterface $cacheIdStrategy,
        LoggerInterface $logger
    ) {
        $this->cacheManager = $cacheManager;
        $this->cacheIdStrategy = $cacheIdStrategy;
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
    public function accept(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function process(CacheItem $cacheItem, TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType)
    {
        if ($cacheItem->getCacheStatus() !== CacheItem::STATUS_OK && $cacheItem->getCacheStatus() !== CacheItem::STATUS_DELEGATE) {
            $this->cacheManager->deleteCacheItem($cacheItem);

            return null;
        }

        return $cacheItem;
    }
}
