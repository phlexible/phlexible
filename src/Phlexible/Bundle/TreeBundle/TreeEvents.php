<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle;

/**
 * Tree events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeEvents
{
    /**
     * Fired before a new tree node is created.
     */
    const BEFORE_CREATE_NODE = 'phlexible_tree.before_create_node';

    /**
     * Fired after a new tree node has been created.
     */
    const CREATE_NODE = 'phlexible_tree.create_node';

    /**
     * Fired before a tree node instance is created.
     */
    const BEFORE_CREATE_NODE_INSTANCE = 'phlexible_tree.before_create_node_instance';

    /**
     * Fired after a tree node instance has been created.
     */
    const CREATE_NODE_INSTANCE = 'phlexible_tree.create_node_instance';

    /**
     * Fired before a tree node is updated.
     */
    const BEFORE_UPDATE_NODE = 'phlexible_tree.before_update_node';

    /**
     * Fired after a tree node has updated.
     */
    const UPDATE_NODE = 'phlexible_tree.update_node';

    /**
     * Fired before a node is deleted.
     */
    const BEFORE_DELETE_NODE = 'phlexible_tree.before_delete_node';

    /**
     * Fired after a node is deleted.
     */
    const DELETE_NODE = 'phlexible_tree.delete_node';

    /**
     * Fired before a tree node is published.
     */
    const BEFORE_PUBLISH_NODE = 'phlexible_tree.before_publish_node';

    /**
     * Fired after a tree node is published.
     */
    const PUBLISH_NODE = 'phlexible_tree.publish_node';

    /**
     * Fired before a tree node is set offline.
     */
    const BEFORE_SET_NODE_OFFLINE = 'phlexible_tree.before_set_node_offline';

    /**
     * Fired after a node is set offline.
     */
    const SET_NODE_OFFLINE = 'phlexible_tree.set_node_offline';

    /**
     * Fired before a tree node is moved.
     */
    const BEFORE_MOVE_NODE = 'phlexible_tree.before_move_node';

    /**
     * Fired after a tree node has been moved.
     */
    const MOVE_NODE = 'phlexible_tree.move_node';

    /**
     * Fired before a tree nodes sort mode is changed.
     */
    const BEFORE_SET_NODE_SORT_MODE = 'phlexible_tree.before_set_node_sort_mode';

    /**
     * Fired after a tree nodes sort mode has been changed.
     */
    const SET_NODE_SORT_MODE = 'phlexible_tree.set_node_sort_mode';

    /**
     * Fired before tree node is reordered.
     */
    const BEFORE_REORDER_NODE = 'phlexible_tree.before_reorder_node';

    /**
     * Fired after tree nodes have been reordered.
     */
    const REORDER_NODE = 'phlexible_tree.reorder_node';

    /**
     * Fired before node children are reordered.
     */
    const BEFORE_REORDER_CHILD_NODE = 'phlexible_tree.before_reorder_child_nodes';

    /**
     * Fired after node children have been reordered.
     */
    const REORDER_CHILD_NODES = 'phlexible_tree.reorder_child_nodes';

    /**
     * Fired when the tree is filtered
     */
    const TREE_FILTER = 'phlexible_tree.tree_filter';

}
