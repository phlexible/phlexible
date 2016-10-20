<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Model;

/**
 * Elementtype structure iterator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeStructureIterator implements \RecursiveIterator
{
    /**
     * @var ElementtypeStructure
     */
    private $tree;

    /**
     * @var string
     */
    private $dsId;

    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @param ElementtypeStructure $tree
     * @param string               $dsId
     */
    public function __construct(ElementtypeStructure $tree, $dsId = null)
    {
        $this->tree = $tree;
        $this->dsId = $dsId;

        $this->iterator = $dsId
            ? new \ArrayIterator($tree->getChildNodes($dsId))
            : new \ArrayIterator([$tree->getRootNode()]);
    }

    /**
     * @return ElementtypeStructureNode
     */
    public function current()
    {
        // delegate to internal iterator
        return $this->iterator->current();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->current()->getDsId();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        // delegate to internal iterator
        $this->iterator->next();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        // delegate to internal iterator
        $this->iterator->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        // delegate to internal iterator
        return $this->iterator->valid();
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return $this->tree->hasChildNodes($this->dsId);
    }

    /**
     * @return ElementtypeStructureIterator
     */
    public function getChildren()
    {
        return new ElementtypeStructureIterator($this->tree, $this->key());
    }

}
