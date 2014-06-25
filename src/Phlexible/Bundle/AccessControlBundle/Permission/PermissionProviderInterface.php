<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Permission;

/**
 * Permission provider interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface PermissionProviderInterface
{
    /**
     * Return permissions
     *
     * @return array
     */
    public function getPermissions();
}
