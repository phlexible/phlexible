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
