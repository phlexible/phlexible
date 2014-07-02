<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CacheBundle\Cache;

use Doctrine\Common\Cache\Cache;

/**
 * Cache collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CacheCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var array
     */
    private $caches = array();

    /**
     * @param Cache[] $caches
     */
    public function __construct(array $caches)
    {
        foreach ($caches as $name => $cache) {
            $this->addCache($name, $cache);
        }
    }

    /**
     * @param string $name
     * @param Cache  $cache
     * @return $this
     */
    public function addCache($name, Cache $cache)
    {
        $this->caches[$name] = $cache;
        return $this;
    }

    /**
     * @return Cache[]
     */
    public function getAll()
    {
        return $this->caches;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->caches);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->caches);
    }
}
