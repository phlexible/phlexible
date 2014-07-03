<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietery
 */

namespace Phlexible\Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * User bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleUserBundle extends Bundle
{
    const RESOURCE_USERS = 'users';
    const RESOURCE_USERS_IMPERSONATE = 'users_impersonate';
    const RESOURCE_GROUPS = 'groups';
}
