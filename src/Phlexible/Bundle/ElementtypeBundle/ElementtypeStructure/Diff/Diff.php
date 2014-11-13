<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\Diff;

use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;

/**
 * Diff
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Diff
{
    private $added = [];
    private $moved = [];
    private $removed = [];

    /**
     * @param \Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode $newNode
     *
     * @return $this
     */
    public function addAdded(ElementtypeStructureNode $newNode)
    {
        $this->added[] = $newNode;

        return $this;
    }

    /**
     * @return array
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * @param ElementtypeStructureNode $oldNode
     * @param ElementtypeStructureNode $newNode
     *
     * @return $this
     */
    public function addMoved(ElementtypeStructureNode $oldNode, ElementtypeStructureNode $newNode)
    {
        $this->moved[] = ['oldNode' => $oldNode, 'newNode' => $newNode];

        return $this;
    }

    /**
     * @return array
     */
    public function getMoved()
    {
        return $this->moved;
    }

    /**
     * @param \Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode $oldNode
     *
     * @return $this
     */
    public function addRemoved(ElementtypeStructureNode $oldNode)
    {
        $this->removed[] = $oldNode;

        return $this;
    }

    /**
     * @return array
     */
    public function getRemoved()
    {
        return $this->removed;
    }
}
