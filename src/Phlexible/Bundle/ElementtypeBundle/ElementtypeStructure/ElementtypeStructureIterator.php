<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure;

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
            : new \ArrayIterator(array($tree->getRootNode()));
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
     * @return string
     */
    public function key()
    {
        return $this->current()->getDsId();
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
