<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Before reorder node event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReorderNodeEvent extends NodeEvent
{
    /**
     * @var TreeNodeInterface
     */
    private $parentNode;

    /**
     * @var bool
     */
    private $before;

    /**
     * @param TreeNodeInterface $node
     * @param TreeNodeInterface $parentNode
     * @param bool              $before
     */
    public function __construct(TreeNodeInterface $node, TreeNodeInterface $parentNode, $before)
    {
        parent::__construct($node);

        $this->parentNode = $parentNode;
        $this->before = (bool) $before;
    }

    /**
     * @return \Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface
     */
    public function getParentNode()
    {
        return $this->parentNode;
    }

    /**
     * @return bool
     */
    public function getBefore()
    {
        return $this->before;
    }
}
