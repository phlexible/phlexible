<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\AccessControlBundle\Entity\AccessControlEntry;
use Phlexible\Component\AccessControl\AccessControlEvents;
use Phlexible\Component\AccessControl\Event\AccessControlEntryEvent;
use Phlexible\Component\AccessControl\Model\AccessControlList;
use Phlexible\Component\AccessControl\Model\AccessManagerInterface;
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
     * @var EntityRepository
     */
    private $accessControlEntryRepository;

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
    }

    /**
     * @return EntityRepository
     */
    private function getAccessControlEntryRepository()
    {
        if (null === $this->accessControlEntryRepository) {
            $this->accessControlEntryRepository = $this->entityManager->getRepository('PhlexibleAccessControlBundle:AccessControlEntry');
        }

        return $this->accessControlEntryRepository;
    }

    /**
     * @param ObjectIdentityInterface $objectIdentity
     *
     * @return AccessControlList
     */
    public function findAcl(ObjectIdentityInterface $objectIdentity)
    {
        $acl = new AccessControlList($this->permissionRegistry->get($objectIdentity->getType()), $objectIdentity);

        $aces = $this->getAccessControlEntryRepository()
            ->findBy(
                array(
                    'objectType' => $objectIdentity->getType(),
                    'objectId' => $objectIdentity->getIdentifier(),
                )
            );

        foreach ($aces as $ace) {
            $acl->addAccessControlEntry($ace);
        }

        return $acl;
    }

    /**
     * {@inheritdoc}
     */
    public function updateAcl(AccessControlList $acl)
    {
        die("test");
        $event = new AccessControlEntryEvent($ace);
        if ($this->dispatcher->dispatch(AccessControlEvents::BEFORE_SET_RIGHT, $event)->isPropagationStopped()) {
            return $this;
        }

        $this->entityManager->persist($ace);
        $this->entityManager->flush($ace);

        $event = new AccessControlEntryEvent($ace);
        $this->dispatcher->dispatch(AccessControlEvents::SET_RIGHT, $event);

        return $this;
    }
}
