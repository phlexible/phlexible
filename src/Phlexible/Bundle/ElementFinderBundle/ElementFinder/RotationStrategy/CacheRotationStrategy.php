<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\RotationStrategy;

use Doctrine\Common\Cache\Cache;
use Phlexible\Bundle\ElementFinderBundle\ElementFinder\ElementFinderResultPool;

/**
 * Cache rotations strategy
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CacheRotationStrategy implements RotationStrategyInterface
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastRotationPosition(ElementFinderResultPool $pool)
    {
        $identifier = $pool->getIdentifier();

        $position = $this->cache->fetch($identifier);

        if (!isset($position) || !$position) {
            $position = 0;
            $this->cache->save($identifier, $position);
        }

        return (int) $position;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastRotationPosition(ElementFinderResultPool $pool, $position)
    {
        $identifier = $pool->getIdentifier();

        $this->cache->save($identifier, $position);

        return $this;
    }
}