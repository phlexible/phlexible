<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\RoleProvider;

use Phlexible\Bundle\GuiBundle\Security\RoleProvider\RoleProvider;

/**
 * Element role provider.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementRoleProvider extends RoleProvider
{
    /**
     * {@inheritdoc}
     */
    public function provideRoles()
    {
        return [
            'ROLE_ELEMENTS',
            'ROLE_ELEMENT_CHILDREN',
            'ROLE_ELEMENT_COMMENT',
            'ROLE_ELEMENT_CONFIG',
            'ROLE_ELEMENT_CREATE',
            'ROLE_ELEMENT_DELETE',
            'ROLE_ELEMENT_INSTANCES',
            'ROLE_ELEMENT_LOCKS',
            'ROLE_ELEMENT_META',
            'ROLE_ELEMENT_PUBLISH',
            'ROLE_ELEMENT_VERSIONS',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function provideRoleHierarchy()
    {
        return [
            'ROLE_ELEMENT_ADMIN' => $this->provideRoles(),
            'ROLE_ELEMENT_MANAGER' => [
                'ROLE_ELEMENTS',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function exposeRoles()
    {
        return array_keys($this->provideRoleHierarchy());
    }
}
