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
     * @var array
     */
    private $typeMap = array();

    /**
     * @var array
     */
    private $bitMap = array();

    /**
     * @param array $permissions
     */
    public function __construct(array $permissions = array())
    {
        foreach ($permissions as $permission) {
            $this->add($permission);
        }
    }

    /**
     * @param PermissionProviderInterface $provider
     *
     * @return $this
     */
    public function addProvider(PermissionProviderInterface $provider)
    {
        $this->addCollection($provider->getPermissions());

        return $this;
    }

    /**
     * @param PermissionCollection $permissions
     *
     * @return $this
     */
    public function addCollection(PermissionCollection $permissions)
    {
        foreach ($permissions->getAll() as $type => $permission) {
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
        $type = $permission->getType();
        $name = $permission->getName();
        $bit = $permission->getBit();

        if (isset($this->bitMap[$type]) && ($this->bitMap[$type] & $bit)) {
            throw new InvalidArgumentException("Permission bit $bit is already set for $type.");
        }

        if (isset($this->typeMap[$type][$name])) {
            throw new InvalidArgumentException("Permission name $name is already set for $type.");
        }

        if (!isset($this->bitMap[$type])) {
            $this->bitMap[$type] = 0;
        }

        $this->permissions[$name] = $permission;
        $this->typeMap[$type][$name] = $permission;
        $this->bitMap[$type] |= $bit;

        return $this;
    }

    /**
     * Return all permissions
     *
     * @return array
     */
    public function getAll()
    {
        return $this->permissions;
    }

    /**
     * Return rights
     *
     * @param string $type
     *
     * @throws InvalidArgumentException
     * @return Permission[]
     */
    public function getByType($type)
    {
        if (!isset($this->typeMap[$type])) {
            throw new InvalidArgumentException("No permissions for type $type found.");
        }

        return $this->typeMap[$type];
    }

    /**
     * @param string $type
     * @param string $permission
     *
     * @return bool
     */
    public function has($type, $permission)
    {
        return !empty($this->permissions[$type][$permission]);
    }
}
