<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Model;

use Phlexible\Bundle\TreeBundle\Entity\TreeNodeOnline;

/**
 * Tree interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TreeInterface
{
    const SORT_MODE_TITLE = 'title';
    const SORT_MODE_CREATEDATE = 'createdate';
    const SORT_MODE_PUBLISHDATE = 'publishdate';
    const SORT_MODE_CUSTOMDATE = 'customdate';
    const SORT_MODE_FREE = 'free';

    const SORT_DIR_ASC = 'asc';
    const SORT_DIR_DESC = 'desc';

    /**
     * @return string
     */
    public function getSiterootId();

    /**
     * Return the root node.
     *
     * @return TreeNodeInterface
     */
    public function getRoot();

    /**
     * Return a node.
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
     * @param mixed       $typeId
     * @param string|null $type
     *
     * @return TreeNodeInterface[]
     */
    public function getByTypeId($typeId, $type = null);

    /**
     * @param mixed       $typeId
     * @param string|null $type
     *
     * @return bool
     */
    public function hasByTypeId($typeId, $type = null);

    /**
     * Return child nodes.
     *
     * @param TreeNodeInterface $node
     *
     * @return TreeNodeInterface[]
     */
    public function getChildren(TreeNodeInterface $node);

    /**
     * Are child nodes present?
     *
     * @param TreeNodeInterface $node
     *
     * @return bool
     */
    public function hasChildren(TreeNodeInterface $node);

    /**
     * Return parent node.
     *
     * @param TreeNodeInterface $node
     *
     * @return TreeNodeInterface
     */
    public function getParent(TreeNodeInterface $node);

    /**
     * Return ID path array.
     *
     * @param TreeNodeInterface $node
     *
     * @return array
     */
    public function getIdPath(TreeNodeInterface $node);

    /**
     * Return node path array.
     *
     * @param TreeNodeInterface $node
     *
     * @return TreeNodeInterface[]
     */
    public function getPath(TreeNodeInterface $node);

    /**
     * Is the given node the root node?
     *
     * @param TreeNodeInterface $node
     *
     * @return bool
     */
    public function isRoot(TreeNodeInterface $node);

    /**
     * Is childId a child of parentId?
     *
     * @param TreeNodeInterface $childNode
     * @param TreeNodeInterface $parentNode
     *
     * @return bool
     */
    public function isChildOf(TreeNodeInterface $childNode, TreeNodeInterface $parentNode);

    /**
     * Is parentId a parent of childId?
     *
     * @param TreeNodeInterface $parentNode
     * @param TreeNodeInterface $childNode
     *
     * @return bool
     */
    public function isParentOf(TreeNodeInterface $parentNode, TreeNodeInterface $childNode);

    /**
     * @param TreeNodeInterface $node
     *
     * @return bool
     */
    public function isInstance(TreeNodeInterface $node);

    /**
     * @param TreeNodeInterface $node
     *
     * @return bool
     */
    public function isInstanceMaster(TreeNodeInterface $node);

    /**
     * Return instance nodes for the given nodes from THIS tree.
     *
     * @param TreeNodeInterface $node
     *
     * @return TreeNodeInterface[]
     */
    public function getInstances(TreeNodeInterface $node);

    /**
     * @param TreeNodeInterface $treeNode
     *
     * @return array
     */
    public function getSavedLanguages(TreeNodeInterface $treeNode);

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return bool
     */
    public function isPublished(TreeNodeInterface $node, $language);

    /**
     * @param TreeNodeInterface $node
     *
     * @return array
     */
    public function getPublishedLanguages(TreeNodeInterface $node);

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return int|null
     */
    public function getPublishedVersion(TreeNodeInterface $node, $language);

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return \DateTime|null
     */
    public function getPublishedAt(TreeNodeInterface $node, $language);

    /**
     * @param TreeNodeInterface $node
     *
     * @return array
     */
    public function getPublishedVersions(TreeNodeInterface $node);

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return bool
     */
    public function isAsync(TreeNodeInterface $node, $language);

    /**
     * @param TreeNodeInterface $node
     *
     * @return TreeNodeOnline[]
     */
    public function findOnlineByTreeNode(TreeNodeInterface $node);

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return TreeNodeOnline
     */
    public function findOneOnlineByTreeNodeAndLanguage(TreeNodeInterface $node, $language);
}
