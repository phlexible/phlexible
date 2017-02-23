<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * User bundle.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleUserBundle extends Bundle
{
    const RESOURCE_USERS = 'users';
    const RESOURCE_USERS_IMPERSONATE = 'users_impersonate';
    const RESOURCE_GROUPS = 'groups';
}
