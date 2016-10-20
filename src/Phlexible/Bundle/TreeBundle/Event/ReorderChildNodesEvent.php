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
 * Reorder child nodes event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReorderChildNodesEvent extends NodeEvent
{
    /**
     * @var array
     */
    private $sortIds;

    /**
     * @param TreeNodeInterface $node
     * @param array             $sortIds
     */
    public function __construct(TreeNodeInterface $node, array $sortIds)
    {
        parent::__construct($node);

        $this->sortIds = $sortIds;
    }

    /**
     * @return array
     */
    public function getSortIds()
    {
        return $this->sortIds;
    }
}
