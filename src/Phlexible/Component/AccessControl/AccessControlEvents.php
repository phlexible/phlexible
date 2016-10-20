<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
