<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Permission;

/**
 * Permission collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PermissionCollection
{
    /**
     * @var array
     */
    private $permissions = array();

    /**
     * @param array $rights
     */
    public function __construct(array $rights)
    {
        $this->permissions = $rights;
    }

    /**
     * Return rights
     *
     * @param string $rightType
     * @param string $contentType
     *
     * @return array
     */
    public function getPermissions($rightType = null, $contentType = null)
    {
        $permissions = $this->permissions;

        if (null !== $rightType) {
            if (!isset($permissions[$rightType])) {
                return array();
            }
            $permissions = $permissions[$rightType];

            if (null !== $contentType) {
                if (!isset($permissions[$contentType])) {
                    return array();
                }
                $permissions = $permissions[$contentType];
            }
        }

        return $permissions;
    }

    /**
     * @param string $rightType
     * @param string $contentType
     * @param string $permission
     *
     * @return boolean
     */
    public function hasPermission($rightType, $contentType, $permission)
    {
        return !empty($this->permissions[$rightType][$contentType][$permission]);
    }
}
