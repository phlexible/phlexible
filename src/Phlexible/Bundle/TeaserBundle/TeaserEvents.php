<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle;

/**
 * Teaser events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserEvents
{
    /**
     * Before Catch Get Result Pool
     *
     * Fired before catch queries the database.
     */
    const BEFORE_CATCH_GET_RESULT_POOL = 'phlexible_teaser.before_catch_get_result_pool';

    /**
     * Before Catch Get Result Pool
     *
     * Fired before catch queries the database.
     */
    const CATCH_GET_RESULT_POOL = 'phlexible_teaser.catch_get_result_pool';

    /**
     * Before Delete Catch Event
     *
     * Fired before a catch is created.
     */
    const BEFORE_CREATE_CATCH = 'phlexible_teaser.before_create_catch';

    /**
     * Delete Catch Event
     *
     * Fired after a catch has been created.
     */
    const CREATE_CATCH = 'phlexible_teaser.create_catch';

    /**
     * Before Update Catch Event
     *
     * Fired before a catch is updated.
     */
    const BEFORE_UPDATE_CATCH = 'phlexible_teaser.before_update_catch';

    /**
     * Delete Update Event
     *
     * Fired after a catch has been updated.
     */
    const UPDATE_CATCH = 'phlexible_teaser.update_catch';

    /**
     * Before Delete Catch Event
     *
     * Fired before a teaser is deleted.
     */
    const BEFORE_DELETE_CATCH = 'phlexible_teaser.before_delete_catch';

    /**
     * Delete Catch Event
     *
     * Fired after a catch has been deleted.
     */
    const DELETE_CATCH = 'phlexible_teaser.delete_catch';

    /**
     * Before Create Teaser Event
     *
     * Fired before a teaser is created.
     */
    const BEFORE_CREATE_TEASER = 'phlexible_teaser.before_create_teaser';

    /**
     * Create Teaser Event
     *
     * Fired after a teaser has been created.
     */
    const CREATE_TEASER = 'phlexible_teaser.create_teaser';

    /**
     * Before Create Teaser Instance Event
     *
     * Fired before a teaser instance is created.
     */
    const BEFORE_CREATE_TEASER_INSTANCE = 'phlexible_teaser.before_create_teaser_instance';

    /**
     * Create Teaser Instance Event
     *
     * Fired after a teaser instance has been created.
     */
    const CREATE_TEASER_INSTANCE = 'phlexible_teaser.create_teaser_instance';

    /**
     * Before Delete Teaser Event
     *
     * Fired before a teaser is deleted.
     */
    const BEFORE_DELETE_TEASER = 'phlexible_teaser.before_delete_teaser';

    /**
     * Delete Teaser Event
     *
     * Fired after a teaser has been deleted.
     */
    const DELETE_TEASER = 'phlexible_teaser.delete_teaser';

    /**
     * Before Update Teaser Event
     *
     * Fired before a teaser is updated.
     */
    const BEFORE_UPDATE_TEASER = 'phlexible_teasers.before_update_teaser';

    /**
     * Update Teaser Event
     *
     * Fired after a teaser has been updated.
     */
    const UPDATE_TEASER = 'phlexible_teaser.update_teaser';

    /**
     * Before Publish Teaser Event
     *
     * Fired before a teaser is published.
     */
    const BEFORE_PUBLISH_TEASER = 'phlexible_teaser.before_publish_teaser';

    /**
     * Publish Teaser Event
     *
     * Fired after a teaser is published.
     */
    const PUBLISH_TEASER = 'phlexible_teaser.publish_teaser';

    /**
     * Before Set Teaser Offline Event
     *
     * Fired before a teaser is set offline.
     */
    const BEFORE_SET_TEASER_OFFLINE = 'phlexible_teaser.before_set_teaser_offline';

    /**
     * Set Teaser Offline Event
     *
     * Fired after a teaser is set offline.
     */
    const SET_TEASER_OFFLINE = 'phlexible_teaser.set_teaser_offline';

    /**
     * Before Show Teaser Event
     *
     * Fired before a teaser is shown.
     */
    const BEFORE_SHOW_TEASER = 'phlexible_teaser.before_show_teaser';

    /**
     * Show Teaser Event
     *
     * Fired after a teaser is shown.
     */
    const SHOW_TEASER = 'phlexible_teaser.show_teaser';

    /**
     * Before Hide Teaser Event
     *
     * Fired before a teaser is hidden.
     */
    const BEFORE_HIDE_TEASER = 'phlexible_teaser.before_hide_teaser';

    /**
     * Hide Teaser Event
     *
     * Fired after a teaser is published.
     */
    const HIDE_TEASER = 'phlexible_teaser.hide_teaser';

    /**
     * Before Inherit Teaser Event
     *
     * Fired before a teaser is inherited.
     */
    const BEFORE_INHERIT_TEASER = 'phlexible_teaser.before_inherit_teaser';

    /**
     * Inherit Teaser Event
     *
     * Fired after a teaser is inherited.
     */
    const INHERIT_TEASER = 'phlexible_teaser.inherit_teaser';

    /**
     * Before Stop Inherit Teaser Event
     *
     * Fired before a teaser is stopped from inherit.
     */
    const BEFORE_STOP_INHERIT_TEASER = 'phlexible_teaser.before_stop_inherit_teaser';

    /**
     * Stop Inherit Teaser Event
     *
     * Fired after a teaser is stopped from inherit.
     */
    const STOP_INHERIT_TEASER = 'phlexible_teaser.stop_inherit_teaser';

    /**
     * Before Show Inherited Teaser Event
     *
     * Fired before an inherited teaser is shown.
     */
    const BEFORE_SHOW_INHERITED_TEASER = 'phlexible_teaser.before_show_inherited_teaser';

    /**
     * Show Inherited Teaser Event
     *
     * Fired after an inherited teaser is shown.
     */
    const SHOW_INHERITED_TEASER = 'phlexible_teaser.show_inherited_teaser';

    /**
     * Before Hide Inherited Teaser Event
     *
     * Fired before an inherited teaser is hidden.
     */
    const BEFORE_HIDE_INHERITED_TEASER = 'phlexible_teaser.before_hide_inherited_teaser';

    /**
     * Hide Inherited Teaser Event
     *
     * Fired after an inherited teaser is published.
     */
    const HIDE_INHERITED_TEASER = 'phlexible_teaser.hide_inherited_teaser';

    /**
     * Before Inherit Inherited Teaser Event
     *
     * Fired before an inherited teaser is inherited.
     */
    const BEFORE_INHERIT_INHERITED_TEASER = 'phlexible_teaser.before_inherit_inherited_teaser';

    /**
     * Inherit Inherited Teaser Event
     *
     * Fired after an inherited teaser is inherited.
     */
    const INHERIT_INHERITED_TEASER = 'phlexible_teaser.inherit_inherited_teaser';

    /**
     * Before Stop Inherit Inherited Teaser Event
     *
     * Fired before an inherited teaser is stopped from inherit.
     */
    const BEFORE_STOP_INHERIT_INHERITED_TEASER = 'phlexible_teaser.before_stop_inherit_inherited_teaser';

    /**
     * Stop Inherit Inherited Teaser Event
     *
     * Fired after an inherited teaser is stopped from inherit.
     */
    const STOP_INHERIT_INHERITED_TEASER = 'phlexible_teaser.stop_inherit_inherited_teaser';

    /**
     * Update Catch Teaser Helper Event
     *
     * Fired before a catch teaser helper is updates.
     */
    const BEFORE_UPDATE_CATCH_TEASER_HELPER = 'phlexible_teaser.before_update_catch_teaser_helper';

    /**
     * Update Catch Teaser Helper Event
     *
     * Fired after a catch teaser helper is updates.
     */
    const UPDATE_CATCH_TEASER_HELPER = 'phlexible_teaser.update_catch_teaser_helper';

    /**
     * Before Reorder Nodes Event
     *
     * Fired before tree nodes are reordered.
     */
    const BEFORE_REORDER_TEASERS = 'phlexible_teaser.before_reorder_teasers';

    /**
     * Reorder Nodes Event
     *
     * Fired after tree nodes have been reordered.
     */
    const REORDER_TEASERS = 'phlexible_teaser.reorder_teasers';
}
