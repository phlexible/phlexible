<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Before move node event.
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
