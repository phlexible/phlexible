<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Component\AccessControl\Model\AccessManagerInterface;
use Phlexible\Component\AccessControl\Model\DomainObjectInterface;
use Phlexible\Component\AccessControl\Model\HierarchicalObjectIdentity;
use Phlexible\Component\AccessControl\Permission\PermissionRegistry;
use SplObjectStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Node permission resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodePermissionResolver
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var AccessManagerInterface
     */
    private $accessManager;

    /**
     * @var PermissionRegistry
     */
    private $permissionRegistry;

    /**
     * @var SplObjectStorage
     */
    private $superAdminPermissions;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param AccessManagerInterface        $accessManager
     * @param PermissionRegistry            $permissionRegistry
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        AccessManagerInterface $accessManager,
        PermissionRegistry $permissionRegistry
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->accessManager = $accessManager;
        $this->permissionRegistry = $permissionRegistry;

        $this->superAdminPermissions = new SplObjectStorage();
    }

    /**
     * @param mixed          $object
     * @param string         $language
     * @param TokenInterface $token
     *
     * @return array
     */
    public function resolve($object, $language, TokenInterface $token)
    {
        if (!($object instanceof DomainObjectInterface)) {
            return array();
        }

        $permissionNames = array();

        if ($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            if (!isset($this->superAdminPermissions[$token])) {
                $permissions = $this->permissionRegistry->get(get_class($object))->all();

                foreach ($permissions as $permission) {
                    $permissionNames[] = $permission->getName();
                }

                $this->superAdminPermissions[$token] = $permissionNames;
            }

            return $this->superAdminPermissions[$token];
        }

        if (!$this->authorizationChecker->isGranted(['permission' => 'VIEW', 'language' => $language], $object)) {
            return null;
        }

        $identity = HierarchicalObjectIdentity::fromDomainObject($object);
        $acl = $this->accessManager->findAcl($identity);

        $permissions = $acl->getEffectivePermissions($token, $language);

        foreach ($permissions as $permission) {
            $permissionNames[] = $permission->getName();
        }

        return $permissionNames;
    }
}
