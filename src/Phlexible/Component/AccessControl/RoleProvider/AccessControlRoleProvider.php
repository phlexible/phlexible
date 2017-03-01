<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl\RoleProvider;

use Phlexible\Bundle\GuiBundle\Security\RoleProvider\RoleProvider;

/**
 * Access control role provider.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AccessControlRoleProvider extends RoleProvider
{
    /**
     * {@inheritdoc}
     */
    public function provideRoles()
    {
        return array(
            'ROLE_ACCESS_CONTROL',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function exposeRoles()
    {
        return $this->provideRoles();
    }
}
