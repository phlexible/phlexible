<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle;

/**
 * User events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface UserEvents
{
    /**
     * Before create user event
     * Fired before a new user has been created
     */
    const BEFORE_CREATE_USER = 'phlexible_user.before_create_user';

    /**
     * Create user event
     * Fired after a new user has been created
     */
    const CREATE_USER = 'phlexible_user.create_user';

    /**
     * Before update user event
     * Fired after a user has been updated
     */
    const BEFORE_UPDATE_USER = 'phlexible_user.before_update_user';

    /**
     * Update user event
     * Fired after a user has been updated
     */
    const UPDATE_USER = 'phlexible_user.update_user';

    /**
     * Before apply successor event
     * Fired before a user is applied as a successor of another user
     */
    const BEFORE_APPLY_SUCCESSOR = 'phlexible_user.before_apply_successor';

    /**
     * Apply successor
     * Fired after a user has been applied as a successor of another user
     */
    const APPLY_SUCCESSOR = 'phlexible_user.apply_successor';

    /**
     * Before delete user event
     * Fired before a user has been deleted
     */
    const BEFORE_DELETE_USER = 'phlexible_user.before_delete_user';

    /**
     * Delete user event
     * Fired after a user has been deleted
     */
    const DELETE_USER = 'phlexible_user.delete_user';

    /**
     * Before create group event
     * Fired before a new group has been created
     */
    const BEFORE_CREATE_GROUP = 'phlexible_user.before_create_group';

    /**
     * Create group event
     * Fired after a new group has been created
     */
    const CREATE_GROUP = 'phlexible_user.after_create_group';

    /**
     * Before update group event
     * Fired after a group has been updated
     */
    const BEFORE_UPDATE_GROUP = 'phlexible_user.before_update_group';

    /**
     * Update group event
     * Fired after a group has been updated
     */
    const UPDATE_GROUP = 'phlexible_user.update_group';

    /**
     * Before delete group event
     * Fired before a group has been deleted
     */
    const BEFORE_DELETE_GROUP = 'phlexible_user.before_delete_group';

    /**
     * Delete group event
     * Fired after a group has been deleted
     */
    const DELETE_GROUP = 'phlexible_user.after_delete_group';
}
