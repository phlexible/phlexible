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
     * Fired before catch queries the database.
     */
    const BEFORE_CATCH_GET_RESULT_POOL = 'phlexible_element_finder.before_catch_get_result_pool';

    /**
     * Fired before catch queries the database.
     */
    const CATCH_GET_RESULT_POOL = 'phlexible_element_finder.catch_get_result_pool';

    /**
     * Fired before a catch is created.
     */
    const BEFORE_CREATE_CATCH = 'phlexible_element_finder.before_create_catch';

    /**
     * Fired after a catch has been created.
     */
    const CREATE_CATCH = 'phlexible_element_finder.create_catch';

    /**
     * Fired before a catch is updated.
     */
    const BEFORE_UPDATE_CATCH = 'phlexible_element_finder.before_update_catch';

    /**
     * Fired after a catch has been updated.
     */
    const UPDATE_CATCH = 'phlexible_element_finder.update_catch';

    /**
     * Fired before a teaser is deleted.
     */
    const BEFORE_DELETE_CATCH = 'phlexible_element_finder.before_delete_catch';

    /**
     * Fired after a catch has been deleted.
     */
    const DELETE_CATCH = 'phlexible_element_finder.delete_catch';

    /**
     * Fired before a catch teaser helper is updates.
     */
    const BEFORE_UPDATE_LOOKUP_ELEMENT = 'phlexible_element_finder.before_update_lookup_element';

    /**
     * Fired after a catch teaser helper is updates.
     */
    const UPDATE_LOOKUP_ELEMENT = 'phlexible_element_finder.update_lookup_element';
}
