<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
