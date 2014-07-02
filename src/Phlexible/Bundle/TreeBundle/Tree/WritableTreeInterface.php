<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Phlexible\Bundle\TreeBundle\Exception\InvalidNodeMoveException;
use Phlexible\Bundle\TreeBundle\Tree\Node\TreeNodeInterface;

/**
 * Writable tree interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface WritableTreeInterface
{
    /**
     * Add a node
     *
     * @param TreeNodeInterface|int $parentNode
     * @param TreeNodeInterface|int $afterNode
     * @param string                $type
     * @param string                $typeId
     * @param array                 $attributes
     * @param string                $uid
     * @param string                $sortMode
     * @param string                $sortDir
     *
     * @return TreeNodeInterface
     */
    public function add($parentNode, $afterNode, $type, $typeId, array $attributes, $uid, $sortMode = 'free', $sortDir = 'asc');

    /**
     * Reorder node
     *
     * @param TreeNodeInterface|int $node
     * @param TreeNodeInterface|int $targetNode
     * @param bool                  $before
     *
     * @throws InvalidNodeMoveException
     */
    public function reorder($node, $targetNode, $before = false);

    /**
     * Move node
     *
     * @param TreeNodeInterface|int $node
     * @param TreeNodeInterface|int $toNode
     * @param string                $uid
     */
    public function move($node, $toNode, $uid);

    /**
     * Delete node
     *
     * @param TreeNodeInterface|int $node
     * @param string                $uid
     * @param string                $comment
     */
    public function delete($node, $uid, $comment = null);
}
