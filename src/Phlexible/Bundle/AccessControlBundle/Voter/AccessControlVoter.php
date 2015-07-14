<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Voter;

use Phlexible\Component\AccessControl\Domain\ObjectIdentity;
use Phlexible\Component\AccessControl\Exception\InvalidArgumentException;
use Phlexible\Component\AccessControl\Model\AccessManagerInterface;
use Phlexible\Component\AccessControl\Model\DomainObjectInterface;
use Phlexible\Component\AccessControl\Model\HierarchicalDomainObjectInterface;
use Phlexible\Component\AccessControl\Model\HierarchicalObjectIdentity;
use Phlexible\Component\AccessControl\Model\ObjectIdentityInterface;
use Phlexible\Component\AccessControl\Model\UserSecurityIdentity;
use Phlexible\Component\AccessControl\Permission\PermissionRegistry;
use Phlexible\Component\AccessControl\Rights\CalculatedRights;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Rights voter
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AccessControlVoter implements VoterInterface
{
    const RIGHT_STATUS_UNSET = -1;
    const RIGHT_STATUS_STOPPED = 0;
    const RIGHT_STATUS_SINGLE = 1;
    const RIGHT_STATUS_INHERITABLE = 2;
    const RIGHT_STATUS_INHERITED = 3;
    const RIGHT_STATUS_STOPPED_UNSET = 4;

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
     * @param bool                   $permissiveOnEmpty
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
        $securityIdentity = UserSecurityIdentity::fromToken($token);

        if ($acl->check($permission, $securityIdentity, $objectLanguage)) {
            return self::ACCESS_GRANTED;
        }

        return self::ACCESS_DENIED;

        $user = $token->getUser();

        $rightIdentifiers = array(
            array('type' => 'uid', 'id' => $user->getId())
        );
        foreach ($user->getGroups() as $groupId) {
            $rightIdentifiers[] = array('type' => 'gid', 'id' => $groupId);
        }

        $calculatedRights = $this->calculateRights($rightType, $object, $rightIdentifiers);
        if ($calculatedRights->hasRight($right, $language)) {
            return self::ACCESS_GRANTED;
        } else {
            return self::ACCESS_DENIED;
        }
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

    /**
     * Calculates the contacts rights on the given tree-node
     *
     * @param string                 $rightType
     * @param ContentObjectInterface $contentObject
     * @param array                  $rightIdentifiers
     *
     * @return CalculatedRights
     */
    public function calculateRights(
        $rightType,
        ContentObjectInterface $contentObject,
        array $rightIdentifiers = array())
    {
        $calculatedRights = new CalculatedRights();

        // get the path from the root down to this object
        $contentObjectPath = $contentObject->getContentObjectPath();
        $contentObjectIdentifiers = $contentObject->getContentObjectIdentifiers();

        // fetch rights delivered by group-memberships
        foreach ($rightIdentifiers as $identifier) {
            $subjectType = $identifier['type'];
            $subjectId = $identifier['id'];
            $effectiveRights = $this->fetchEffectiveRights(
                $subjectType,
                $subjectId,
                $rightType,
                $contentObjectIdentifiers['type'],
                $contentObjectIdentifiers['id'],
                $contentObjectPath
            );

            if (!is_array($effectiveRights)) {
                continue;
            }

            foreach ($effectiveRights as $language => $row) {
                if (is_array($row)) {
                    $calculatedRights->add($language, $row);
                }
            }
        }

        return $calculatedRights;
    }

    /**
     * fetches effective rights, regaring inheritance etc. for the given object-type
     * with the given id
     *
     * @param string $objectType
     * @param int    $objectId
     * @param string $rightType
     * @param string $contentType
     * @param int    $contentId
     * @param array  $path
     *
     * @throws \Phlexible\Component\AccessControl\Exception\InvalidArgumentException
     * @return array
     */
    public function fetchRights($objectType, $objectId, $rightType, $contentType, $contentId, array $path)
    {
        if (!count($path)) {
            return null;
        }

        $rights = array();

        foreach ($path as $pathId) {
            $result = $this->accessManager->findBy(
                array(
                    'right_type'   => $rightType,
                    'content_type' => $contentType,
                    'content_id'   => $pathId,
                    'object_type'  => $objectType,
                    'object_id'    => $objectId
                )
            );

            foreach ($result as $row) {
                $langage = $row['content_language'] ? $row['content_language'] : '_all_';
                $right = $row['right'];

                switch ((int) $row['inherit']) {
                    case self::RIGHT_STATUS_SINGLE:
                    case self::RIGHT_STATUS_INHERITABLE:
                    case self::RIGHT_STATUS_STOPPED:
                        $rights[$langage][$right] = array(
                            'type'        => (int) $row['inherit'],
                            'objectType'  => $objectType,
                            'objectId'    => $objectId,
                            'pathId'      => $pathId,
                            'contentType' => $contentType,
                            'contentId'   => $pathId,
                        );
                        break;

                    default:
                        $msg = 'Unknown status for right "' . $right . '" -> "' . $row['inherit'] . '"';
                        throw new InvalidArgumentException($msg);
                        break;
                }
            }
        }

        // clean single rights
        foreach ($rights as $language => $languageRow) {
            foreach ($languageRow as $key => $right) {
                if ($right['type'] == self::RIGHT_STATUS_SINGLE) {
                    if ($right['contentId'] != $contentId) {
                        $rights[$key]['type'] = self::RIGHT_STATUS_STOPPED_UNSET;
                    }
                }
            }
        }

        return $rights;
    }

    /**
     * fetches effective rights, regarding inheritance etc. for the given object-type
     * with the given id
     *
     * @param string $objectType
     * @param int    $objectId
     * @param string $rightType
     * @param string $contentType
     * @param int    $contentId
     * @param array  $path
     *
     * @return array
     */
    public function fetchEffectiveRights($objectType, $objectId, $rightType, $contentType, $contentId, array $path)
    {
        $rights = $this->fetchRights($objectType, $objectId, $rightType, $contentType, $contentId, $path);

        if (count($rights)) {
            foreach ($rights as $language => $languageRow) {
                // clean single rights
                foreach ($languageRow as $key => $right) {
                    if ($right['type'] == self::RIGHT_STATUS_SINGLE) {
                        if ($right['contentId'] != $contentId) {
                            unset($rights[$language][$key]);
                        }
                    } elseif ($right['type'] == self::RIGHT_STATUS_UNSET
                        //|| $right['type'] == self::RIGHT_STATUS_STOPPED
                        || $right['type'] == self::RIGHT_STATUS_STOPPED_UNSET
                    ) {
                        unset($rights[$language][$key]);
                    }
                }
            }
        }

        return $rights;
    }
}
