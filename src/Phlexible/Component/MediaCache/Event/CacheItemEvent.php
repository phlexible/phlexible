<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\Event;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Symfony\Component\EventDispatcher\Event;

/**
 * Cache item event
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
