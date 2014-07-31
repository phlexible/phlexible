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
    private $classMaps = array();

    /**
     * @var array
     */
    private $bitMaps = array();

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
        foreach ($permissions->getAll() as $permission) {
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
        $contentClass = $permission->getContentClass();
        $name = $permission->getName();
        $bit = $permission->getBit();

        if (isset($this->bitMaps[$contentClass]) && ($this->bitMaps[$contentClass] & $bit)) {
            throw new InvalidArgumentException("Permission bit $bit is already set for $contentClass.");
        }

        if (isset($this->classMaps[$contentClass][$name])) {
            throw new InvalidArgumentException("Permission name $name is already set for $contentClass.");
        }

        if (!isset($this->bitMaps[$contentClass])) {
            $this->bitMaps[$contentClass] = 0;
        }

        $this->classMaps[$contentClass][$name] = $permission;
        $this->bitMaps[$contentClass] |= $bit;
        $this->permissions[] = $permission;

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
     * Return permissions for content class
     *
     * @param string $contentClass
     *
     * @throws InvalidArgumentException
     * @return Permission[]
     */
    public function getByContentClass($contentClass)
    {
        if (!isset($this->classMaps[$contentClass])) {
            throw new InvalidArgumentException("No permissions for type $contentClass found.");
        }

        return $this->classMaps[$contentClass];
    }

    /**
     * Return permissions
     *
     * @param string $contentClass
     * @param string $name
     *
     * @throws InvalidArgumentException
     * @return Permission[]
     */
    public function get($contentClass, $name)
    {
        if (!isset($this->classMaps[$contentClass][$name])) {
            throw new InvalidArgumentException("Permissions $name for content class $contentClass not found.");
        }

        return $this->classMaps[$contentClass][$name];
    }

    /**
     * @param string $contentClass
     * @param string $name
     *
     * @return bool
     */
    public function has($contentClass, $name)
    {
        return !empty($this->classMaps[$contentClass][$name]);
    }
}
