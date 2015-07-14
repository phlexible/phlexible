<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Permission;

use Phlexible\Component\AccessControl\Exception\InvalidArgumentException;

/**
 * Permission collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PermissionCollection
{
    /**
     * @var string
     */
    private $objectType;

    /**
     * @var array
     */
    private $permissions = array();

    /**
     * @var array
     */
    private $bitMap = 0;

    /**
     * @param string $objectType
     * @param array  $permissions
     */
    public function __construct($objectType, array $permissions = array())
    {
        $this->objectType = $objectType;

        foreach ($permissions as $permission) {
            $this->add($permission);
        }
    }

    /**
     * @return string
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * @param PermissionCollection $permissions
     *
     * @return $this
     */
    public function addCollection(PermissionCollection $permissions)
    {
        if ($permissions->getObjectType() !== $this->getObjectType()) {
            throw new InvalidArgumentException("Mismating object types.");
        }

        foreach ($permissions->all() as $permission) {
            $this->add($permission);
        }

        return $this;
    }

    /**
     * @param Permission $permission
     *
     * @throws InvalidArgumentException
     * @return $this
     */
    public function add(Permission $permission)
    {
        $name = $permission->getName();

        if (isset($this->permissions[$name])) {
            throw new InvalidArgumentException("Permission name $name is already set.");
        }

        $bit = $permission->getBit();

        if ($this->bitMap & $bit) {
            throw new InvalidArgumentException("Permission bit $bit is already set.");
        }

        $this->bitMap |= $bit;
        $this->permissions[$name] = $permission;

        return $this;
    }

    /**
     * Return all permissions
     *
     * @return Permission[]
     */
    public function all()
    {
        return $this->permissions;
    }

    /**
     * Return permissions
     *
     * @param string $name
     *
     * @throws InvalidArgumentException
     * @return Permission
     */
    public function get($name)
    {
        if (!isset($this->permissions[$name])) {
            throw new InvalidArgumentException("Permissions $name for type $objectType not found.");
        }

        return $this->permissions[$name];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return !empty($this->permissions[$name]);
    }
}
