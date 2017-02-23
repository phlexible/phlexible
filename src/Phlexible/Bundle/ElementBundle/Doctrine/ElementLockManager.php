<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementLock;
use Phlexible\Bundle\ElementBundle\Entity\Repository\ElementLockRepository;
use Phlexible\Bundle\ElementBundle\Exception\LockFailedException;
use Phlexible\Bundle\ElementBundle\Model\ElementLockManagerInterface;

/**
 * Element lock manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Phillip Look <pl@brainbits.net>
 */
class ElementLockManager implements ElementLockManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ElementLockRepository
     */
    private $lockRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return ElementLockRepository
     */
    public function getLockRepository()
    {
        if (null === $this->lockRepository) {
            $this->lockRepository = $this->entityManager->getRepository('PhlexibleElementBundle:ElementLock');
        }

        return $this->lockRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked(Element $element)
    {
        $locks = $this->getLockRepository()->findByElement($element);

        return count($locks) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isLockedByUser(Element $element, $userId)
    {
        $locks = $this->getLockRepository()->findByElementAndUserId($element, $userId);

        return count($locks) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isLockedByOtherUser(Element $element, $userId)
    {
        $locks = $this->getLockRepository()->findByElementAndNotUserId($element, $userId);

        return count($locks) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function lock(Element $element, $userId, $type = ElementLock::TYPE_TEMPORARY)
    {
        if ($this->isLockedByOtherUser($element, $userId)) {
            throw new LockFailedException('Can\'t aquire lock, already locked.');
        }

        $lock = new ElementLock();
        $lock
            ->setElement($element)
            ->setType($type)
            ->setUserId($userId)
            ->setLockedAt(new \DateTime());

        $this->entityManager->persist($lock);
        $this->entityManager->flush($lock);

        return $lock;
    }

    /**
     * {@inheritdoc}
     */
    public function unlock(Element $element)
    {
        $locks = $this->getLockRepository()->findBy(['element' => $element]);

        foreach ($locks as $lock) {
            $this->entityManager->remove($lock);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findLock(Element $element)
    {
        $lock = $this->getLockRepository()->findOneBy(['element' => $element]);

        return $lock;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getLockRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getLockRepository()->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $sort = [], $limit = null, $offset = null)
    {
        return $this->getLockRepository()->findBy($criteria, $sort, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteLock(ElementLock $lock)
    {
        $this->entityManager->remove($lock);
        $this->entityManager->flush($lock);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAll()
    {
        $qb = $this->getLockRepository()->createQueryBuilder('l');
        $qb->delete();
        $qb->getQuery()->execute();
    }
}
