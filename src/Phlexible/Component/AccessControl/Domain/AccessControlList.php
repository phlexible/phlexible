<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl\Domain;

use Phlexible\Component\AccessControl\Model\HierarchicalObjectIdentity;
use Phlexible\Component\AccessControl\Model\ObjectIdentityInterface;
use Phlexible\Component\AccessControl\Model\SecurityIdentityInterface;
use Phlexible\Component\AccessControl\Permission\HierarchyMaskResolver;
use Phlexible\Component\AccessControl\Permission\Permission;
use Phlexible\Component\AccessControl\Permission\PermissionCollection;
use Phlexible\Component\AccessControl\Permission\PermissionResolver;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Access control list.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AccessControlList implements \Countable
{
    /**
     * @var PermissionCollection
     */
    private $permissions;

    /**
     * @var ObjectIdentityInterface
     */
    private $objectIdentity;

    /**
     * @var Entry[]
     */
    private $entries;

    /**
     * @param PermissionCollection    $permissions
     * @param ObjectIdentityInterface $objectIdentity
     * @param Entry[]                 $accessControlEntries
     */
    public function __construct(
        PermissionCollection $permissions,
        ObjectIdentityInterface $objectIdentity,
        array $accessControlEntries = array()
    ) {
        $this->permissions = $permissions;
        $this->objectIdentity = $objectIdentity;
        $this->entries = $accessControlEntries;
    }

    /**
     * @return PermissionCollection
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return ObjectIdentityInterface
     */
    public function getObjectIdentity()
    {
        return $this->objectIdentity;
    }

    /**
     * @return Entry[]
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param Entry $ace
     *
     * @return $this
     */
    public function addEntry(Entry $ace)
    {
        $this->entries[] = $ace;

        return $this;
    }

    /**
     * @param Entry $ace
     *
     * @return $this
     */
    public function removeEntry(Entry $ace)
    {
        foreach ($this->entries as $index => $entry) {
            if ($ace->getId() === $entry->getId()) {
                unset($this->entries[$index]);
            }
        }

        return $this;
    }

    /**
     * @param Permission     $permission
     * @param TokenInterface $token
     * @param string|null    $objectLanguage
     *
     * @return bool
     */
    public function check(Permission $permission, TokenInterface $token, $objectLanguage = null)
    {
        $user = $token->getUser();

        $masks = $this->getEffectiveMasks();
        if (isset($masks['Phlexible\Bundle\UserBundle\Entity\User'][$user->getId()])) {
            if ($masks['Phlexible\Bundle\UserBundle\Entity\User'][$user->getId()] & $permission->getBit()) {
                return true;
            }
        }
        foreach ($user->getGroups() as $group) {
            if (isset($masks['Phlexible\Bundle\UserBundle\Entity\Group'][$group->getId()])) {
                if ($masks['Phlexible\Bundle\UserBundle\Entity\Group'][$group->getId()] & $permission->getBit()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param TokenInterface $token
     * @param string|null    $objectLanguage
     *
     * @return array
     */
    public function getEffectivePermissions(TokenInterface $token, $objectLanguage = null)
    {
        $user = $token->getUser();

        $mask = 0;
        $masks = $this->getEffectiveMasks();
        if (isset($masks['Phlexible\Bundle\UserBundle\Entity\User'][$user->getId()])) {
            $mask |= $masks['Phlexible\Bundle\UserBundle\Entity\User'][$user->getId()];
        }
        foreach ($user->getGroups() as $group) {
            if (isset($masks['Phlexible\Bundle\UserBundle\Entity\Group'][$group->getId()])) {
                $mask |= $masks['Phlexible\Bundle\UserBundle\Entity\Group'][$group->getId()];
            }
        }

        $resolver = new PermissionResolver();

        return $resolver->resolve($this->permissions, $mask);
    }

    /**
     * @var array
     */
    private $effectiveMasks = null;

    /**
     * @return array
     */
    private function getEffectiveMasks()
    {
        if ($this->effectiveMasks === null) {
            $masks = array();

            $oi = $this->getObjectIdentity();
            if ($oi instanceof HierarchicalObjectIdentity) {
                $map = array();
                foreach ($oi->getHierarchicalIdentifiers() as $identifier) {
                    foreach ($this->getEntries() as $entry) {
                        if ($entry->getObjectIdentifier() === $identifier) {
                            $map[$entry->getSecurityType()][$entry->getSecurityIdentifier()][$entry->getObjectIdentifier()] = $entry;
                        }
                    }
                }

                $maskResolver = new HierarchyMaskResolver();

                foreach ($map as $securityType => $securityIdentifiers) {
                    foreach ($securityIdentifiers as $securityIdentifier => $entries) {
                        $resolvedMasks = $maskResolver->resolve($entries, $oi->getIdentifier());
                        $masks[$securityType][$securityIdentifier] = $resolvedMasks['effectiveMask'];
                    }
                }
            } else {
                foreach ($this->getEntries() as $entry) {
                    $masks[$entry->getSecurityType()][$entry->getSecurityIdentifier()] = $entry->getMask();
                }
            }

            $this->effectiveMasks = $masks;
        }

        return $this->effectiveMasks;
    }

    /**
     * @param SecurityIdentityInterface $securityIdentity
     * @param int                       $mask
     * @param int                       $noInheritMask
     * @param int                       $stopMask
     * @param string|null               $contentLanguage
     *
     * @return $this
     */
    public function setAce(
        SecurityIdentityInterface $securityIdentity,
        $mask,
        $noInheritMask,
        $stopMask,
        $contentLanguage = null
    ) {
        if ($contentLanguage === '_all_') {
            $contentLanguage = null;
        }

        $ace = null;
        foreach ($this->entries as $entry) {
            if ($entry->getSecurityIdentifier() === $securityIdentity->getIdentifier() && $entry->getSecurityType() === $securityIdentity->getType()) {
                $ace = $entry;
                break;
            }
        }

        if (!$ace) {
            $ace = new Entry();
            $ace
                ->setObjectType($this->objectIdentity->getType())
                ->setObjectId($this->objectIdentity->getIdentifier())
                ->setSecurityType($securityIdentity->getType())
                ->setSecurityId($securityIdentity->getIdentifier())
                ->setObjectLanguage($contentLanguage);
        }

        $ace
            ->setMask((int) $mask)
            ->setStopMask((int) $stopMask)
            ->setNoInheritMask((int) $noInheritMask);

        return $this;
    }

    /**
     * @param SecurityIdentityInterface $securityIdentity
     * @param string|null               $objectLanguage
     *
     * @return $this
     */
    public function removeAce(SecurityIdentityInterface $securityIdentity = null, $objectLanguage = null)
    {
        $aces = null;
        foreach ($this->entries as $index => $entry) {
            if ($entry->getSecurityIdentifier() === $securityIdentity->getIdentifier() && $entry->getSecurityType() === $securityIdentity->getType()) {
                unset($this->entries[$index]);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->entries);
    }
}
