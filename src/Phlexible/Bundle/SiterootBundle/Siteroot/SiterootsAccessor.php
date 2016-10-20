<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SiterootBundle\Siteroot;

use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;

/**
 * Siteroots accessor
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class SiterootsAccessor implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @param SiterootManagerInterface $siterootManager
     */
    public function __construct(SiterootManagerInterface $siterootManager)
    {
        $this->siterootManager = $siterootManager;
    }

    /**
     * Whether a offset exists
     *
     * @param mixed $offset
     *
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return $this->siterootManager->find($offset) !== null;
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->siterootManager->find($offset);
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
    }

    /**
     * Retrieve an external iterator
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->siterootManager->findAll());
    }
}
