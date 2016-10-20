<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Util\CanonicalizerInterface;
use Phlexible\Bundle\UserBundle\Event\UserEvent;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Bundle\UserBundle\Successor\SuccessorService;
use Phlexible\Bundle\UserBundle\UserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * User manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserManager extends BaseUserManager implements UserManagerInterface
{
    /**
     * @var EntityRepository
     */
    private $userRepository;

    /**
     * @var SuccessorService
     */
    private $successorService;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var string
     */
    private $systemUserId;

    /**
     * @var string
     */
    private $everyoneGroupId;

    /**
     * @var string
     */
    private $userClass;

    /**
     * @param EncoderFactoryInterface  $encoderFactory
     * @param CanonicalizerInterface   $usernameCanonicalizer
     * @param CanonicalizerInterface   $emailCanonicalizer
     * @param ObjectManager            $om
     * @param string                   $class
     * @param SuccessorService         $successorService
     * @param EventDispatcherInterface $dispatcher
     * @param string                   $systemUserId
     * @param string                   $everyoneGroupId
     */
    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        CanonicalizerInterface $usernameCanonicalizer,
        CanonicalizerInterface $emailCanonicalizer,
        ObjectManager $om,
        $class,
        SuccessorService $successorService,
        EventDispatcherInterface $dispatcher,
        $systemUserId,
        $everyoneGroupId)
    {
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer, $om, $class);

        $this->successorService = $successorService;
        $this->dispatcher = $dispatcher;
        $this->systemUserId = $systemUserId;
        $this->everyoneGroupId = $everyoneGroupId;
    }

    /**
     * @return EntityRepository
     */
    private function getUserRepository()
    {
        if ($this->userRepository === null) {
            $this->userRepository = $this->objectManager->getRepository($this->getClass());
        }

        return $this->userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($userId)
    {
        return $this->getUserRepository()->find($userId);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getUserRepository()->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function countAll()
    {
        $qb = $this->getUserRepository()->createQueryBuilder('u');
        $qb
            ->select('COUNT(u.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByUsername($username)
    {
        return $this->findOneBy(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getUserRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function countBy(array $criteria)
    {
        $qb = $this->getUserRepository()->createQueryBuilder('u');
        $qb
            ->select('COUNT(u.id)');

        foreach ($criteria as $key => $value) {
            $qb->andWhere($qb->expr()->eq($key, $qb->expr()->literal($value)));
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, $order = [])
    {
        return $this->getUserRepository()->findOneBy($criteria, $order);
    }

    /**
     * {@inheritdoc}
     */
    public function search(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->getUserRepository()->createQueryBuilder('u');

        if ($orderBy) {
            foreach ($orderBy as $field => $order) {
                $qb->addOrderBy("u.$field", $order);
            }
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        $this->applySearch($criteria, $qb);

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countSearch(array $criteria)
    {
        $qb = $this->getUserRepository()->createQueryBuilder('u');
        $qb->select($qb->expr()->count('u.id'));

        $this->applySearch($criteria, $qb);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array        $criteria
     * @param QueryBuilder $qb
     */
    private function applySearch(array $criteria, QueryBuilder $qb)
    {
        if (isset($criteria['term'])) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('u.username', $qb->expr()->literal("%{$criteria['term']}%")),
                $qb->expr()->like('u.email', $qb->expr()->literal("%{$criteria['term']}%")),
                $qb->expr()->like('u.firstname', $qb->expr()->literal("%{$criteria['term']}%")),
                $qb->expr()->like('u.lastname', $qb->expr()->literal("%{$criteria['term']}%"))
            ));
        }

        if (isset($criteria['isExpired'])) {
            $qb->andWhere($qb->expr()->isNotNull('u.expiresAt'));
            $qb->andWhere($qb->expr()->lte('u.expiresAt', $qb->expr()->literal(date('Y-m-d H:i:s'))));
        }

        if (isset($criteria['hasExpireDate'])) {
            $qb->andWhere($qb->expr()->isNotNull('u.expiresAt'));
        }

        if (isset($criteria['roles']) and is_array($criteria['roles'])) {
            $or = $qb->expr()->orX();
            foreach ($criteria['roles'] as $role) {
                $or->add($qb->expr()->like('u.roles', $qb->expr()->literal("%$role%")));
            }
            $qb->andWhere($or);
        }

        if (isset($criteria['groups']) and is_array($criteria['groups'])) {
            $groups = array();
            foreach ($criteria['groups'] as $group) {
                $groups[] = $qb->expr()->literal($group);
            }
            $qb->join('u.groups', 'g');
            $qb->andWhere($qb->expr()->in('g.id', $groups));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSystemUserId()
    {
        return $this->systemUserId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSystemUserName()
    {
        return $this->getSystemUser()->getUsername();
    }

    /**
     * {@inheritdoc}
     */
    public function getSystemUser()
    {
        return $this->find($this->getSystemUserId());
    }

    /**
     * {@inheritdoc}
     */
    public function findLoggedInUsers()
    {
        $qb = $this->getUserRepository()->createQueryBuilder('u');
        $qb->where($qb->expr()->gte('u.modifiedAt', $qb->expr()->literal(date('Y-m-d H:i:s'))));

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function checkUsername($username)
    {
        return $this->findOneBy(['username' => $username]) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function checkEmail($email)
    {
        return $this->findOneBy(['email' => $email]) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function updateUser(UserInterface $user, $andFlush = true)
    {
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);

        $isUpdate = false;
        if ($this->objectManager->contains($user)) {
            $isUpdate = true;
        }

        $event = new UserEvent($user);
        if ($isUpdate) {
            $this->dispatcher->dispatch(UserEvents::BEFORE_UPDATE_USER, $event);
        } else {
            $this->dispatcher->dispatch(UserEvents::BEFORE_CREATE_USER, $event);
        }
        if ($event->isPropagationStopped()) {
            return;
        }

        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }

        $event = new UserEvent($user);
        if ($isUpdate) {
            $this->dispatcher->dispatch(UserEvents::UPDATE_USER, $event);
        } else {
            $this->dispatcher->dispatch(UserEvents::CREATE_USER, $event);
        }
    }

    /**
     * @param UserInterface $user
     * @param UserInterface $successorUser
     */
    public function deleteUserWithSuccessor(UserInterface $user, UserInterface $successorUser)
    {
        $this->successorService->set($user, $successorUser);

        $event = new UserEvent($user);
        if ($this->dispatcher->dispatch(UserEvents::BEFORE_DELETE_USER, $event)->isPropagationStopped()) {
            return;
        }

        $this->deleteUser($user);

        $event = new UserEvent($user);
        $this->dispatcher->dispatch(UserEvents::DELETE_USER, $event);
    }
}
