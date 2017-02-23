<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Security\RoleProvider;

/**
 * Role provider interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface RoleProviderInterface
{
    /**
     * Provide simple roles.
     *
     * @return array
     */
    public function provideRoles();

    /**
     * Provide role hierarchy.
     *
     * @return array
     */
    public function provideRoleHierarchy();

    /**
     * Expose roles.
     *
     * @return array
     */
    public function exposeRoles();
}
