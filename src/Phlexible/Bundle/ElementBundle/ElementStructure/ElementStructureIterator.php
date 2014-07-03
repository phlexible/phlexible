<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementStructure;

/**
 * Element structure iterator
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
     * @return string
     */
    public function key()
    {
        return $this->current()->getId();
    }

    public function next()
    {
        // delegate to internal iterator
        $this->iterator->next();
    }

    public function rewind()
    {
        // delegate to internal iterator
        $this->iterator->rewind();
    }

    /**
     * @return bool
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
        return new ElementStructureIterator($this->current()->getStructures());
    }

}
