<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Tree\Node\TreeNodeInterface;

/**
 * Before move node event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MoveNodeEvent extends NodeEvent
{
    /**
     * @var TreeNodeInterface
     */
    private $parentNode;

    /**
     * @param TreeNodeInterface $node
     * @param TreeNodeInterface $parentNode
     */
    public function __construct(TreeNodeInterface $node, TreeNodeInterface $parentNode)
    {
        parent::__construct($node);

        $this->parentNode = $parentNode;
    }

    /**
     * @return TreeNodeInterface
     */
    public function getParentNode()
    {
        return $this->parentNode;
    }
}