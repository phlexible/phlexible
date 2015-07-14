<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl;

/**
 * Access control events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AccessControlEvents
{
    /**
     * Called before updating an ACE
     */
    const BEFORE_UPDATE_ACE = 'phlexible_access_control.before_update_ace';

    /**
     * Called after update of an ACE
     */
    const UPDATE_ACE = 'phlexible_access_control.update_ace';
}
