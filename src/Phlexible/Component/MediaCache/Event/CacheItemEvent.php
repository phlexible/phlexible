<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Event;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Symfony\Component\EventDispatcher\Event;

/**
 * Cache item event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CacheItemEvent extends Event
{
    /**
     * @var CacheItem
     */
    private $cacheItem;

    /**
     * @param CacheItem $cacheItem
     */
    public function __construct(CacheItem $cacheItem)
    {
        $this->cacheItem = $cacheItem;
    }

    /**
     * @return CacheItem
     */
    public function getCacheItem()
    {
        return $this->cacheItem;
    }
}
