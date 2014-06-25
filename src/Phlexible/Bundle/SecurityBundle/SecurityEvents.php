<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle;

/**
 * Security events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SecurityEvents
{
    /**
     * Login view event
     */
    const VIEW_LOGIN = 'phlexible_security.view_login';

    /**
     * Change password view event
     */
    const VIEW_CHANGE_PASSWORD = 'phlexible_security.view_change_password';

    /**
     * Validate view event
     */
    const VIEW_VALIDATE = 'phlexible_security.view_validate';

    /**
     * Set password view event
     */
    const VIEW_SET_PASSWORD = 'phlexible_security.view_set_password';
}
