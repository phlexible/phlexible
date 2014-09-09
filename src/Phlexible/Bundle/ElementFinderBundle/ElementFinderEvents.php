<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle;

/**
 * Element finder events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderEvents
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
}
