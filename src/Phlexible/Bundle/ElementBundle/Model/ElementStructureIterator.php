<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Model;

/**
 * Element structure iterator.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementStructureIterator implements \RecursiveIterator
{
    /**
     * @var ElementStructure
     */
    protected $elementStructure;

    /**
     * @var \Iterator
     */
    protected $iterator;

    /**
     * @param ElementStructure[] $elementStructures
     */
    public function __construct(array $elementStructures)
    {
        $this->iterator = new \ArrayIterator($elementStructures);
    }

    /**
     * @return ElementStructure
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
        return $this->current()->getId();
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
        return count($this->current()->getStructures());
    }

    /**
     * @return ElementStructureIterator
     */
    public function getChildren()
    {
        return new self($this->current()->getStructures());
    }
}
