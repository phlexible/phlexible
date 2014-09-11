<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementLock;
use Phlexible\Bundle\ElementBundle\Entity\Repository\ElementLockRepository;
use Phlexible\Bundle\ElementBundle\Exception\LockFailedException;
use Phlexible\Bundle\ElementBundle\Model\ElementLockManagerInterface;

/**
 * Element lock manager
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
    public function isLocked(Element $element, $language)
    {
        if ($this->isMasterLocked($element)) {
            return true;
        }

        if ($element->getMasterLanguage() === $language) {
            return false;
        }

        return $this->isSlaveLocked($element, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function isMasterLocked(Element $element)
    {
        $locks = $this->getLockRepository()->findByElement($element);

        foreach ($locks as $lock) {
            if ($lock->getLanguage() === null) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isSlaveLocked(Element $element, $language)
    {
        $locks = $this->getLockRepository()->findByElement($element);

        foreach ($locks as $lock) {
            if ($lock->getLanguage() === $language) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isLockedByUser(Element $element, $language, $userId)
    {
        if ($this->isMasterLockedByUser($element, $userId)) {
            return true;
        }

        if ($element->getMasterLanguage() === $language) {
            return false;
        }

        return $this->isSlaveLockedByUser($element, $language, $userId);
    }

    /**
     * {@inheritdoc}
     */
    public function isMasterLockedByUser(Element $element, $userId)
    {
        $locks = $this->getLockRepository()->findByElementAndUserId($element, $userId);

        foreach ($locks as $lock) {
            if ($lock->getLanguage() === null) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isSlaveLockedByUser(Element $element, $language, $userId)
    {
        $locks = $this->getLockRepository()->findByElementAndUserId($element, $userId);

        foreach ($locks as $lock) {
            if ($lock->getLanguage() === $language) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isLockedByOtherUser(Element $element, $language, $userId)
    {
        if ($this->isMasterLockedByOtherUser($element, $userId)) {
            return true;
        }

        if ($element->getMasterLanguage() === $language) {
            return false;
        }

        return $this->isSlaveLockedByOtherUser($element, $language, $userId);
    }

    /**
     * {@inheritdoc}
     */
    public function isMasterLockedByOtherUser(Element $element, $userId)
    {
        $locks = $this->getLockRepository()->findByElementAndNotUserId($element, $userId);

        foreach ($locks as $lock) {
            if ($lock->getLanguage() === null) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isSlaveLockedByOtherUser(Element $element, $language, $userId)
    {
        $locks = $this->getLockRepository()->findByElementAndNotUserId($element, $userId);

        foreach ($locks as $lock) {
            if ($lock->getLanguage() === $language) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function lock(Element $element, $userId, $language = null, $type = ElementLock::TYPE_TEMPORARY)
    {
        if (!$language || $element->getMasterLanguage() === $language) {
            if ($this->isMasterLockedByOtherUser($element, $userId)) {
                throw new LockFailedException('Can\'t aquire lock, already locked.');
            }
        } else {
            if ($this->isSlaveLockedByOtherUser($element, $language, $userId)) {
                throw new LockFailedException('Can\'t aquire lock, already locked.');
            }
        }

        $lock = new ElementLock();
        $lock
            ->setElement($element)
            ->setLanguage($language)
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
    public function unlock(Element $element, $language = null)
    {
        if (!$language || $element->getMasterLanguage() === $language) {
            if ($this->isMasterLocked($element)) {
                throw new LockFailedException('Can\'t aquire lock, already locked.');
            }
        } else {
            if ($this->isSlaveLocked($element, $language)) {
                throw new LockFailedException('Can\'t aquire lock, already locked.');
            }
        }

        $lock = $this->getLockRepository()->findOneBy(array('element' => $element, 'language' => $language));

        $this->entityManager->remove($lock);
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
    public function findMasterLock(Element $element)
    {
        return $this->getLockRepository()->findOneBy(array('element' => $element, 'language' => null));
    }

    /**
     * {@inheritdoc}
     */
    public function findSlaveLock(Element $element, $language)
    {
        return $this->getLockRepository()->findOneBy(array('element' => $element, 'language' => $language));
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
    public function findBy(array $criteria, array $sort = array(), $limit = null, $offset = null)
    {
        return $this->getLockRepository()->findBy($criteria, $sort, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteLock(ElementLock $lock)
    {
        $this->entityManager->remove($lock);
        $this->entityManager->flush();
    }
}
