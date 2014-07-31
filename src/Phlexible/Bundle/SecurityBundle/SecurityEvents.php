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
class SecurityEvents
{
    /**
     * Login view event
     */
    const VIEW_LOGIN = 'phlexible_security.view_login';

    /**
     * Force password change view event
     */
    const VIEW_FORCE_PASSWORD_CHANGE = 'phlexible_security.force_password_change';

    /**
     * Validate view event
     */
    const VIEW_VALIDATE_EMAIL = 'phlexible_security.view_validate_email';

    /**
     * Set password view event
     */
    const VIEW_RESET_PASSWORD = 'phlexible_security.view_reset_password';
}
