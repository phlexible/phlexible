<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Security\RoleProvider;

/**
 * Role provider interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface RoleProviderInterface
{
    /**
     * Provide simple roles
     *
     * @return array
     */
    public function provideRoles();

    /**
     * Provide role hierarchy
     *
     * @return array
     */
    public function provideRoleHierarchy();

    /**
     * Expose roles
     *
     * @return array
     */
    public function exposeRoles();
}