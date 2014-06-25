<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Events;
use Phlexible\Bundle\TreeBundle\Tree\Node\TreeNode;

/**
 * Before move node event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeMoveNodeEvent extends AbstractNodeEvent
{
    /**
     * @var string
     */
    protected $eventName = Events::BEFORE_MOVE_NODE;

    /**
     * @var TreeNode
     */
    protected $parentNode = null;

    /**
     * @param TreeNode $node
     * @param TreeNode $parentNode
     */
    public function __construct(TreeNode $node, TreeNode $parentNode)
    {
        parent::__construct($node);

        $this->parentNode = $parentNode;
    }

    /**
     * Return parent node
     *
     * @return TreeNode
     */
    public function getParentNode()
    {
        return $this->parentNode;
    }
}