<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Permission;

use Phlexible\Bundle\AccessControlBundle\Exception\InvalidArgumentException;

/**
 * Permission resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PermissionResolver
{
    /**
     * @var PermissionCollection
     */
    private $permissions;

    /**
     * @param PermissionCollection $permissions
     */
    public function __construct(PermissionCollection $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @param string $type
     * @param int    $mask
     *
     * @throws InvalidArgumentException
     * @return Permission[]
     */
    public function resolve($type, $mask)
    {
        $permissions = array();

        foreach ($this->permissions->getByType($type) as $permission) {
            if ($permission->getBit() & $mask) {
                $permissions[] = $permission;
                $mask = $mask ^ $permission->getBit();
            }
        }

        if ($mask) {
            $bits = decbin($mask);
            throw new InvalidArgumentException("Permission for bits $bits not found.");
        }

        return $permissions;
    }
}
