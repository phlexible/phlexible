<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Security;

use Phlexible\Bundle\GuiBundle\Security\RoleProvider\RoleProviderInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchy;

/**
 * Role hierarchy factory
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RoleHierarchyFactory
{
    /**
     * @var RoleProviderInterface[]
     */
    private $roleProviders = [];

    /**
     * @var array
     */
    private $hierarchy = [];

    /**
     * @param array $hierarchy
     */
    public function __construct(array $hierarchy)
    {
        $this->hierarchy = $hierarchy;
    }

    /**
     * @param RoleProviderInterface $roleProvider
     *
     * @return $this
     */
    public function addRoleProvider(RoleProviderInterface $roleProvider)
    {
        $this->roleProviders[] = $roleProvider;

        return $this;
    }

    /**
     * @return RoleHierarchy
     */
    public function factory()
    {
        $hierarchy = $this->hierarchy;
        $roles = [];
        foreach ($this->roleProviders as $roleProvider) {
            $hierarchy = array_merge($hierarchy, $roleProvider->provideRoleHierarchy());
            $roles = array_merge($roles, $roleProvider->provideRoles());
        }
        $hierarchy['ROLE_SUPER_ADMIN'] = isset($hierarchy['ROLE_SUPER_ADMIN'])
            ? $hierarchy['ROLE_SUPER_ADMIN']
            : [];
        $hierarchy['ROLE_SUPER_ADMIN'] = array_unique(array_merge($hierarchy['ROLE_SUPER_ADMIN'], $roles));

        return new RoleHierarchy($hierarchy);
    }
}
