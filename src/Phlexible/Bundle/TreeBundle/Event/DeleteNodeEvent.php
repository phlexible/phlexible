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
 * Delete node event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DeleteNodeEvent extends NodeEvent
{
    /**
     * @var int
     */
    private $nodeId;

    /**
     * @var string
     */
    private $userId;

    /**
     * @param TreeNodeInterface $node
     * @param int               $nodeId
     * @param string            $userId
     */
    public function __construct(TreeNodeInterface $node, $nodeId, $userId)
    {
        parent::__construct($node);

        $this->nodeId = $nodeId;
        $this->userId = $userId;
    }

    /**
     * Return node ID
     *
     * @return int
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * Return user ID
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
