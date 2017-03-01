<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\RoleProvider;

use Phlexible\Bundle\GuiBundle\Security\RoleProvider\RoleProvider;

/**
 * User role provider.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserRoleProvider extends RoleProvider
{
    /**
     * {@inheritdoc}
     */
    public function provideRoles()
    {
        return [
            'ROLE_USERS',
            'ROLE_GROUPS',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function exposeRoles()
    {
        return $this->provideRoles();
    }
}
