<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle;

/**
 * Access control events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AccessControlEvents
{
    /**
     * Before set right event
     * Called before setting a right
     */
    const BEFORE_SET_RIGHT = 'phlexible_access_control.before_set_right';

    /**
     * Set right event
     * Called after setting a right
     */
    const SET_RIGHT = 'phlexible_access_control.set_right';

    /**
     * Before remove right event
     * Called before removing a right
     */
    const BEFORE_REMOVE_RIGHT = 'phlexible_access_control.before_remove_right';

    /**
     * Remove right event
     * Called after removing a right
     */
    const REMOVE_RIGHT = 'phlexible_access_control.remove_right';
}
