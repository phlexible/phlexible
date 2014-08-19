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
 * Tree interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TreeInterface
{
    const SORT_MODE_TITLE       = 'title';
    const SORT_MODE_CREATEDATE  = 'createdate';
    const SORT_MODE_PUBLISHDATE = 'publishdate';
    const SORT_MODE_CUSTOMDATE  = 'customdate';
    const SORT_MODE_FREE        = 'free';

    const SORT_DIR_ASC  = 'asc';
    const SORT_DIR_DESC = 'desc';

    /**
     * @return string
     */
    public function getSiterootId();

    /**
     * Return the root node
     *
     * @return TreeNodeInterface
     */
    public function getRoot();

    /**
     * Return a node
     *
     * @param int $id
     *
     * @return TreeNodeInterface
     */
    public function get($id);

    /**
     * Has this tree the given Tree ID?
     *
     * @param int $id
     *
     * @return bool
     */
    public function has($id);

    /**
     * Return child nodes
     *
     * @param TreeNodeInterface|int $node
     *
     * @return TreeNodeInterface[]
     */
    public function getChildren($node);

    /**
     * Are child nodes present?
     *
     * @param TreeNodeInterface|int $node
     *
     * @return bool
     */
    public function hasChildren($node);

    /**
     * Return parent node
     *
     * @param TreeNodeInterface|int $node
     *
     * @return TreeNodeInterface
     */
    public function getParent($node);

    /**
     * Return ID path array
     *
     * @param TreeNodeInterface|int $node
     *
     * @return array
     */
    public function getIdPath($node);

    /**
     * Return node path array
     *
     * @param TreeNodeInterface|int $node
     *
     * @return TreeNodeInterface[]
     */
    public function getPath($node);

    /**
     * Is the given node the root node?
     *
     * @param TreeNodeInterface|int $node
     *
     * @return bool
     */
    public function isRoot($node);

    /**
     * Is childId a child of parentId?
     *
     * @param TreeNodeInterface|int $childNode
     * @param TreeNodeInterface|int $parentNode
     *
     * @return bool
     */
    public function isChildOf($childNode, $parentNode);

    /**
     * Is parentId a parent of childId?
     *
     * @param TreeNodeInterface|int $parentNode
     * @param TreeNodeInterface|int $childNode
     *
     * @return bool
     */
    public function isParentOf($parentNode, $childNode);
}
