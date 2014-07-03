<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
     * Before Create Node Event
     *
     * Fired before a new tree node is created
     */
    const BEFORE_CREATE_NODE = 'phlexible_tree.before_create_node';

    /**
     * Create Node Event
     *
     * Fired after a new tree node has been created
     */
    const CREATE_NODE = 'phlexible_tree.create_node';

    const BEFORE_CREATE_NODE_INSTANCE = 'phlexible_tree.before_create_node_instance';
    const CREATE_NODE_INSTANCE = 'phlexible_tree.create_node_instance';

    /**
     * Before Update Node Event
     *
     * Fired before a tree node is updated.
     */
    const BEFORE_UPDATE_NODE = 'phlexible_tree.before_update_node';

    /**
     * Update Node Event
     *
     * Fired after a tree node has updated.
     */
    const UPDATE_NODE = 'phlexible_tree.update_node';

    /**
     * Before Delete Node Event
     *
     * Fired before a node is deleted.
     */
    const BEFORE_DELETE_NODE = 'phlexible_tree.before_delete_node';

    /**
     * Delete Node Event
     *
     * Fired after a node is deleted.
     */
    const DELETE_NODE = 'phlexible_tree.delete_node';

    /**
     * Before Publish Node Event
     *
     * Fired before a tree node is published.
     */
    const BEFORE_PUBLISH_NODE = 'phlexible_tree.before_publish_node';

    /**
     * Publish Node Event
     *
     * Fired after a tree node is published.
     */
    const PUBLISH_NODE = 'phlexible_tree.publish_node';

    /**
     * Before Set Node Offline Event
     *
     * Fired before a tree node is set offline.
     */
    const BEFORE_SET_NODE_OFFLINE = 'phlexible_tree.before_set_node_offline';

    /**
     * Set Node Offline Event
     *
     * Fired after a node is set offline.
     */
    const SET_NODE_OFFLINE = 'phlexible_tree.set_node_offline';

    /**
     * Before Move Node Event
     *
     * Fired before a tree node is moved.
     */
    const BEFORE_MOVE_NODE = 'phlexible_tree.before_move_node';

    /**
     * Move Node Event
     *
     * Fired after a tree node has been moved.
     */
    const MOVE_NODE = 'phlexible_tree.move_node';

    /**
     * Before Set Node Sort Mode Event
     *
     * Fired before a tree nodes sort mode is changed.
     */
    const BEFORE_SET_NODE_SORT_MODE = 'phlexible_tree.before_set_node_sort_mode';

    /**
     * Set Node Sort Mode Event
     *
     * Fired after a tree nodes sort mode has been changed.
     */
    const SET_NODE_SORT_MODE = 'phlexible_tree.set_node_sort_mode';

    /**
     * Before Reorder Nodes Event
     *
     * Fired before tree nodes are reordered.
     */
    const BEFORE_REORDER_NODES = 'phlexible_tree.before_reorder_nodes';

    /**
     * Reorder Nodes Event
     *
     * Fired after tree nodes have been reordered.
     */
    const REORDER_NODES = 'phlexible_tree.reorder_nodes';
}
