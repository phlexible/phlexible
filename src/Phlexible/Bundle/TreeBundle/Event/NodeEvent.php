<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Node event
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
     * Return node
     *
     * @return TreeNodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }
}
