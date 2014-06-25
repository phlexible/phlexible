<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Tree\Node\TreeNode;
use Symfony\Component\EventDispatcher\Event;

/**
 * Abstract node event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractNodeEvent extends Event
{
    /**
     * @var TreeNode
     */
    protected $node = null;

    /**
     * @param TreeNode $node
     */
    public function __construct(TreeNode $node)
    {
        $this->node = $node;
    }

    /**
     * Return node
     *
     * @return TreeNode
     */
    public function getNode()
    {
        return $this->node;
    }
}