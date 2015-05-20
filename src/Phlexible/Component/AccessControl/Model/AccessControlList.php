<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Model;

use Phlexible\Bundle\AccessControlBundle\Entity\AccessControlEntry;
use Phlexible\Component\AccessControl\Permission\Permission;
use Phlexible\Component\AccessControl\Permission\PermissionCollection;

/**
 * Access control list
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AccessControlList
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
     * @var AccessControlEntry[]
     */
    private $entries;

    /**
     * @param PermissionCollection    $permissions
     * @param ObjectIdentityInterface $objectIdentity
     * @param AccessControlEntry[]    $accessControlEntries
     */
    public function __construct(
        PermissionCollection $permissions,
        ObjectIdentityInterface $objectIdentity,
        array $accessControlEntries = array()
    )
    {
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
     * @return AccessControlEntry[]
     */
    public function getAccessControlEntries()
    {
        return $this->entries;
    }

    /**
     * @param AccessControlEntry $ace
     *
     * @return $this
     */
    public function addAccessControlEntry(AccessControlEntry $ace)
    {
        $this->entries[] = $ace;

        return $this;
    }

    /**
     * @param Permission                $permission
     * @param SecurityIdentityInterface $securityIdentity
     * @param string|null               $objectLanguage
     *
     * @return bool
     */
    public function check(Permission $permission, SecurityIdentityInterface $securityIdentity, $objectLanguage = null)
    {
        foreach ($this->entries as $entry) {
            if (
                $entry->getSecurityType() === $securityIdentity->getType() &&
                $entry->getSecurityId() === $securityIdentity->getIdentifier()
            ) {
                return $permission->test($entry->getMask());
            }
        }

        return false;
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
    )
    {
        if ($contentLanguage === '_all_') {
            $contentLanguage = null;
        }

        $ace = null;
        foreach ($this->entries as $entry) {
            if ($entry->getSecurityId() === $securityIdentity->getIdentifier() && $entry->getSecurityType() == $securityIdentity->getType()) {
                $ace = $entry;
                break;
            }
        }

        if (!$ace) {
            $ace = new AccessControlEntry();
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
            if ($entry->getSecurityId() === $securityIdentity->getIdentifier() && $entry->getSecurityType() == $securityIdentity->getType()) {
                unset($this->entries[$index]);
            }
        }

        return $this;
    }
}
