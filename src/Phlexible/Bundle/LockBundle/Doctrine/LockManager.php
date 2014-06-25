<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\LockBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\LockBundle\Entity\Lock;
use Phlexible\Bundle\LockBundle\Entity\Repository\LockRepository;
use Phlexible\Bundle\LockBundle\Exception\LockFailedException;
use Phlexible\Bundle\LockBundle\Lock\LockIdentityInterface;
use Phlexible\Bundle\LockBundle\Model\LockManagerInterface;

/**
 * Lock service
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 */
class LockManager implements LockManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var LockRepository
     */
    private $lockRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->lockRepository = $entityManager->getRepository('PhlexibleLockBundle:Lock');
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked($identifier)
    {
        $lock = $this->find($identifier);

        return $lock !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function isLockedByUser($identifier, $userId)
    {
        if ($identifier instanceof LockIdentityInterface) {
            $identifier = $identifier->__toString();
        }

        $lock = $this->lockRepository->findOneBy(array('identifier' => $identifier, 'userId' => $userId));

        return $lock !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function lock($identifier, $userId, $type = Lock::TYPE_TEMPORARY,
                         $objectType = null, $objectId = null)
    {
        if ($identifier instanceof LockIdentityInterface) {
            $identifier = $identifier->__toString();
        }

        if ($this->isLocked($identifier)) {
            throw new LockFailedException('Can\'t aquire lock, already locked.');
        }

        $lock = new Lock();
        $lock
            ->setId($identifier)
            ->setType($type)
            ->setObjectType($objectType)
            ->setObjectId($objectId)
            ->setUserId($userId);

        $this->entityManager->persist($lock);
        $this->entityManager->flush($lock);

        return $lock;
    }

    /**
     * {@inheritdoc}
     */
    public function unlock($identifier)
    {
        if ($identifier instanceof LockIdentityInterface) {
            $identifier = $identifier->__toString();
        }

        if (!$this->isLocked($identifier)) {
            return;
        }

        $lock = $this->find($identifier);

        $this->entityManager->remove($lock);
    }

    /**
     * {@inheritdoc}
     */
    public function find($identifier)
    {
        if ($identifier instanceof LockIdentityInterface) {
            $identifier = $identifier->__toString();
        }

        return $this->lockRepository->find($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->lockRepository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $sort = array(), $limit = null, $offset = null)
    {
        return $this->lockRepository->findBy($criteria, $sort, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteLock(Lock $lock)
    {
        $this->entityManager->remove($lock);
        $this->entityManager->flush();
    }
}
