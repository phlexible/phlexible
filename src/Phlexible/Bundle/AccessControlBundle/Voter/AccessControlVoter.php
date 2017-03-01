<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\AccessControlBundle\Voter;

use Phlexible\Component\AccessControl\Domain\ObjectIdentity;
use Phlexible\Component\AccessControl\Model\AccessManagerInterface;
use Phlexible\Component\AccessControl\Model\DomainObjectInterface;
use Phlexible\Component\AccessControl\Model\HierarchicalDomainObjectInterface;
use Phlexible\Component\AccessControl\Model\HierarchicalObjectIdentity;
use Phlexible\Component\AccessControl\Model\ObjectIdentityInterface;
use Phlexible\Component\AccessControl\Permission\PermissionRegistry;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Rights voter.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AccessControlVoter implements VoterInterface
{
    /**
     * @var AccessManagerInterface
     */
    private $accessManager;

    /**
     * @var PermissionRegistry
     */
    private $permissionRegistry;

    /**
     * @var PermissionRegistry
     */
    private $permissiveOnEmptyAcl;

    /**
     * @param AccessManagerInterface $accessManager
     * @param PermissionRegistry     $permissionRegistry
     * @param bool                   $permissiveOnEmptyAcl
     */
    public function __construct(AccessManagerInterface $accessManager, PermissionRegistry $permissionRegistry, $permissiveOnEmptyAcl)
    {
        $this->accessManager = $accessManager;
        $this->permissionRegistry = $permissionRegistry;
        $this->permissiveOnEmptyAcl = $permissiveOnEmptyAcl;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $identity, array $attributes)
    {
        if (!$identity instanceof ObjectIdentityInterface) {
            if ($identity instanceof HierarchicalDomainObjectInterface) {
                $identity = HierarchicalObjectIdentity::fromDomainObject($identity);
            } elseif ($identity instanceof DomainObjectInterface) {
                $identity = ObjectIdentity::fromDomainObject($identity);
            } else {
                return self::ACCESS_ABSTAIN;
            }
        }

        $permissionName = !empty($attributes['permission']) ? $attributes['permission'] : $attributes[0];
        $objectLanguage = !empty($attributes['language']) ? $attributes['language'] : null;

        if (!$this->permissionRegistry->has($identity->getType())) {
            return self::ACCESS_ABSTAIN;
        }

        $permissions = $this->permissionRegistry->get($identity->getType());

        if (!$permissions->has($permissionName)) {
            return self::ACCESS_ABSTAIN;
        }

        $permission = $permissions->get($permissionName);

        $acl = $this->accessManager->findAcl($identity);

        if (!count($acl)) {
            if ($this->permissiveOnEmptyAcl) {
                return self::ACCESS_GRANTED;
            } else {
                return self::ACCESS_DENIED;
            }
        }
        //$securityIdentity = UserSecurityIdentity::fromToken($token);

        if ($acl->check($permission, $token, $objectLanguage)) {
            return self::ACCESS_GRANTED;
        }

        return self::ACCESS_DENIED;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return true;
    }
}
