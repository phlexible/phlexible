<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Phlexible\Component\AccessControl\Domain\AccessControlList;
use Phlexible\Component\AccessControl\Domain\Entry;
use Phlexible\Component\AccessControl\Model\AccessManagerInterface;
use Phlexible\Component\AccessControl\Model\HierarchicalObjectIdentity;
use Phlexible\Component\AccessControl\Model\ObjectIdentityInterface;
use Phlexible\Component\AccessControl\Permission\PermissionRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Access manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AccessManager implements AccessManagerInterface
{
    /**
     * @var PermissionRegistry
     */
    private $permissionRegistry;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $objectIds = array();

    /**
     * @param PermissionRegistry       $permissionRegistry
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(PermissionRegistry $permissionRegistry, EntityManager $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->permissionRegistry = $permissionRegistry;
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;

        $this->objectIds = new \SplObjectStorage();
    }

    /**
     * @return Connection
     */
    private function getConnection()
    {
        return $this->entityManager->getConnection();
    }

    /**
     * @param ObjectIdentityInterface $objectIdentity
     *
     * @return int
     */
    private function findObjectIdentityId(ObjectIdentityInterface $objectIdentity)
    {
        if ($this->objectIds->contains($objectIdentity)) {
            return $this->objectIds->offsetGet($objectIdentity);
        }

        $conn = $this->getConnection();

        $qb = $conn->createQueryBuilder();
        $qb
            ->select('id')
            ->from('acl_object_identity')
            ->where($qb->expr()->eq('type', $qb->expr()->literal($objectIdentity->getType())))
            ->andWhere($qb->expr()->eq('identifier', $qb->expr()->literal($objectIdentity->getIdentifier())));

        $id = $qb->execute()->fetchColumn();

        if ($id) {
            $this->objectIds->attach($objectIdentity, $id);
        }

        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function createObjectIdentity(ObjectIdentityInterface $objectIdentity)
    {
        $conn = $this->getConnection();

        if ($this->findObjectIdentityId($objectIdentity)) {
            throw new \Exception("Identity is already persisted.");
        }

        $data = array(
            'type'       => $objectIdentity->getType(),
            'identifier' => $objectIdentity->getIdentifier(),
        );
        $conn->insert('acl_object_identity', $data);

        return $this->findObjectIdentityId($objectIdentity);
    }

    /**
     * @param ObjectIdentityInterface $objectIdentity
     *
     * @return AccessControlList
     */
    public function findAcl(ObjectIdentityInterface $objectIdentity)
    {
        $conn = $this->getConnection();

        $qb = $conn->createQueryBuilder();
        $qb
            ->select(array('oi.type', 'oi.identifier', 'e.security_type', 'e.security_identifier', 'e.mask', 'e.stop_mask', 'e.no_inherit_mask'))
            ->from('acl_object_identity', 'oi')
            ->join('oi', 'acl_entry', 'e', $qb->expr()->eq('oi.id', 'e.object_identity_id'))
            ->where($qb->expr()->eq('oi.type', $qb->expr()->literal($objectIdentity->getType())))
        ;

        if ($objectIdentity instanceof HierarchicalObjectIdentity) {
            $qb->andWhere($qb->expr()->in('oi.identifier', $objectIdentity->getHierarchicalIdentifiers()));
        } else {
            $qb->andWhere($qb->expr()->eq('oi.identifier', $qb->expr()->literal($objectIdentity->getIdentifier())));
        }

        $entries = $qb->execute()->fetchAll();

        if ($objectIdentity instanceof HierarchicalObjectIdentity) {
            //array_multisort($entries, $objectIdentity->getIdentifier());
        }

        $acl = new AccessControlList($this->permissionRegistry->get($objectIdentity->getType()), $objectIdentity);

        foreach ($entries as $entry) {
            $acl->addEntry(
                new Entry(
                    $acl,
                    $entry['type'],
                    $entry['identifier'],
                    $entry['security_type'],
                    $entry['security_identifier'],
                    $entry['mask'] !== null ? (int) $entry['mask'] : null,
                    $entry['stop_mask'] !== null ? (int) $entry['stop_mask'] : null,
                    $entry['no_inherit_mask'] !== null ? (int) $entry['no_inherit_mask'] : null
                )
            );
        }

        return $acl;
    }

    /**
     * {@inheritdoc}
     */
    public function updateAcl(AccessControlList $acl)
    {
        $conn = $this->getConnection();

        $objectIdentityId = $this->findObjectIdentityId($acl->getObjectIdentity());

        if (!$objectIdentityId) {
            $objectIdentityId = $this->createObjectIdentity($acl->getObjectIdentity());
        }

        $conn->delete('acl_entry', array(
            'object_identity_id' => $objectIdentityId,
        ));

        foreach ($acl->getEntries() as $entry) {
            $conn->insert('acl_entry', array(
                'object_identity_id'  => $objectIdentityId,
                'security_type'       => $entry->getSecurityType(),
                'security_identifier' => $entry->getSecurityIdentifier(),
                'mask'                => $entry->getMask(),
                'stop_mask'           => $entry->getStopMask(),
                'no_inherit_mask'     => $entry->getNoInheritMask(),
            ));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAcl(ObjectIdentityInterface $objectIdentity)
    {
        $conn = $this->getConnection();

        $conn->delete('acl_entry', array(
            'type'       => $objectIdentity->getType(),
            'identifier' => $objectIdentity->getIdentifier(),
        ));

        $conn->delete('acl_object_identity', array(
            'type'       => $objectIdentity->getType(),
            'identifier' => $objectIdentity->getIdentifier(),
        ));
    }
}
