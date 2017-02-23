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
use Symfony\Component\EventDispatcher\Event;

/**
 * Node event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeEvent extends Event
{
    /**
     * @var TreeNodeInterface
     */
    private $node;

    /**
     * @param TreeNodeInterface $node
     */
    public function __construct(TreeNodeInterface $node)
    {
        $this->node = $node;
    }

    /**
     * Return node.
     *
     * @return TreeNodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }
}
