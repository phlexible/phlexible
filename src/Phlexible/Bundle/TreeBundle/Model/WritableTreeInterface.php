<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Model;

use Phlexible\Bundle\TreeBundle\Exception\InvalidNodeMoveException;

/**
 * Writable tree interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface WritableTreeInterface
{
    /**
     * Create a node
     *
     * @param TreeNodeInterface $parentNode
     * @param TreeNodeInterface $afterNode
     * @param string            $type
     * @param string            $typeId
     * @param array             $attributes
     * @param string            $userId
     * @param string            $sortMode
     * @param string            $sortDir
     * @param bool              $navigation
     * @param bool              $needAuthentication
     *
     * @return TreeNodeInterface
     */
    public function create(
        TreeNodeInterface $parentNode,
        TreeNodeInterface $afterNode = null,
        $type,
        $typeId,
        array $attributes,
        $userId,
        $sortMode = 'free',
        $sortDir = 'asc',
        $navigation = false,
        $needAuthentication = false
    );

    /**
     * @param TreeNodeInterface $parentNode
     * @param TreeNodeInterface $afterNode
     * @param TreeNodeInterface $sourceNode
     * @param string            $userId
     *
     * @return TreeNodeInterface
     */
    public function createInstance(
        TreeNodeInterface $parentNode,
        TreeNodeInterface $afterNode = null,
        TreeNodeInterface $sourceNode,
        $userId
    );

    /**
     * Reorder node
     *
     * @param TreeNodeInterface $node
     * @param TreeNodeInterface $beforeNode
     *
     * @throws InvalidNodeMoveException
     */
    public function reorder(TreeNodeInterface $node, TreeNodeInterface $beforeNode);

    /**
     * Reorder node
     *
     * @param TreeNodeInterface $node
     * @param array             $sortIds
     *
     * @throws InvalidNodeMoveException
     */
    public function reorderChildren(TreeNodeInterface $node, array $sortIds);

    /**
     * Move node
     *
     * @param TreeNodeInterface $node
     * @param TreeNodeInterface $toNode
     * @param string            $uid
     */
    public function move(TreeNodeInterface $node, TreeNodeInterface $toNode, $uid);

    /**
     * Delete node
     *
     * @param TreeNodeInterface $node
     * @param string            $userId
     * @param string            $comment
     */
    public function delete(TreeNodeInterface $node, $userId, $comment = null);

    /**
     * @param TreeNodeInterface $node
     * @param int               $version
     * @param string            $language
     * @param string            $userId
     * @param string|null       $comment
     */
    public function publish(TreeNodeInterface $node, $version, $language, $userId, $comment = null);

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     * @param string            $userId
     * @param string|null       $comment
     */
    public function setOffline(TreeNodeInterface $node, $language, $userId, $comment = null);
}
