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
 * Role provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RoleProvider implements RoleProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function provideRoles()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function provideRoleHierarchy()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function exposeRoles()
    {
        return array();
    }
}
