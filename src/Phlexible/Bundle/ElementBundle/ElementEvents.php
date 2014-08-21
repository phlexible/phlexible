<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle;

/**
 * Elements events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementEvents
{
    /**
     * Fired before an element is created.
     */
    const BEFORE_CREATE_ELEMENT = 'phlexible_element.before_create_element';

    /**
     * Fired after an element is created.
     */
    const CREATE_ELEMENT = 'phlexible_element.create_element';

    /**
     * Fired before an element version is created.
     */
    const BEFORE_CREATE_ELEMENT_VERSION = 'phlexible_element.before_create_element_version';

    /**
     * Fired after an element version is created.
     */
    const CREATE_ELEMENT_VERSION = 'phlexible_element.create_element_version';

    /**
     * Fired before element is saved.
     */
    const BEFORE_SAVE_ELEMENT = 'phlexible_element.before_save_element';

    /**
     * Fired after element is saved.
     */
    const SAVE_ELEMENT = 'phlexible_element.save_element';

    /**
     * Fired before element data is saved.
     */
    const BEFORE_SAVE_ELEMENT_DATA = 'phlexible_element.before_save_element_data';

    /**
     * Fired after element data is saved.
     */
    const SAVE_ELEMENT_DATA = 'phlexible_element.save_element_data';

    /**
     * Fired before a new tree node is created
     */
    const BEFORE_CREATE_NODE = 'phlexible_element.before_create_node';

    /**
     * Fired after a new tree node has been created
     */
    const CREATE_NODE = 'phlexible_element.create_node';

    /**
     * Fired before a new tree node instance is created
     */
    const BEFORE_CREATE_NODE_INSTANCE = 'phlexible_element.before_create_node_instance';

    /**
     * Fired after a new tree node instance has been created
     */
    const CREATE_NODE_INSTANCE = 'phlexible_element.create_node_instance';

    /**
     * Fired before a tree node is updated.
     */
    const BEFORE_SAVE_NODE_DATA = 'phlexible_element.before_save_node_data';

    /**
     * Fired after a tree node has been loaded.
     */
    const LOAD_DATA = 'phlexible_element.load_data';

    /**
     * Fired after a tree node has been saved.
     */
    const SAVE_NODE_DATA = 'phlexible_element.save_node_data';

    /**
     * Fired before a tree node is updated.
     */
    const BEFORE_UPDATE_NODE = 'phlexible_element.before_update_node';

    /**
     * Fired after a tree node has updated.
     */
    const UPDATE_NODE = 'phlexible_element.update_node';

    /**
     * Fired before a node is deleted.
     */
    const BEFORE_DELETE_NODE = 'phlexible_element.before_delete_node';

    /**
     * Fired after a node is deleted.
     */
    const DELETE_NODE = 'phlexible_element.delete_node';

    /**
     * Fired before a tree node is published.
     */
    const BEFORE_PUBLISH_NODE = 'phlexible_element.before_publish_node';

    /**
     * Fired after a tree node is published.
     */
    const PUBLISH_NODE = 'phlexible_element.publish_node';

    /**
     * Fired before a tree node is set offline.
     */
    const BEFORE_SET_NODE_OFFLINE = 'phlexible_element.before_set_node_offline';

    /**
     * Fired after a node is set offline.
     */
    const SET_NODE_OFFLINE = 'phlexible_element.set_node_offline';

    /**
     * Fired before a tree node is moved.
     */
    const BEFORE_MOVE_NODE = 'phlexible_element.before_move_node';

    /**
     * Fired after a tree node has been moved.
     */
    const MOVE_NODE = 'phlexible_element.move_node';

    /**
     * Fired before a tree nodes sort mode is changed.
     */
    const BEFORE_SET_NODE_SORT_MODE = 'phlexible_element.before_set_node_sort_mode';

    /**
     * Fired after a tree nodes sort mode has been changed.
     */
    const SET_NODE_SORT_MODE = 'phlexible_element.set_node_sort_mode';

    /**
     * Fired before tree nodes are reordered.
     */
    const BEFORE_REORDER_NODES = 'phlexible_element.before_reorder_nodes';

    /**
     * Fired after tree nodes have been reordered.
     */
    const REORDER_NODES = 'phlexible_element.reorder_nodes';

    /**
     * Fired before Element version Custom Titles are cached
     */
    const BEFORE_CACHE_ELEMENT_VERSION_CUSTOM_TITLES = 'phlexible_element.before_cache_element_version_custom_titles';
}
