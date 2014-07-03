<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\UserBundle\Event\UserEvent;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Bundle\UserBundle\Successor\SuccessorService;
use Phlexible\Bundle\UserBundle\UserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * User manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserManager implements UserManagerInterface, UserProviderInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $userRepository;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

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
     * @param EntityManager            $entityManager
     * @param EncoderFactoryInterface  $encoderFactory
     * @param EventDispatcherInterface $dispatcher
     * @param string                   $userClass
     * @param string                   $systemUserId
     * @param string                   $everyoneGroupId
     */
    public function __construct(
        EntityManager $entityManager,
        EncoderFactoryInterface $encoderFactory,
        EventDispatcherInterface $dispatcher,
        $userClass,
        $systemUserId,
        $everyoneGroupId)
    {
        $this->entityManager = $entityManager;
        $this->encoderFactory = $encoderFactory;
        $this->dispatcher = $dispatcher;
        $this->systemUserId = $systemUserId;
        $this->everyoneGroupId = $everyoneGroupId;

        $this->userClass = $userClass;
        $this->userRepository = $entityManager->getRepository($this->userClass);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserClass()
    {
        return $this->userClass;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $userClass = $this->getUserClass();

        return new $userClass();
    }

    /**
     * {@inheritdoc}
     */
    public function find($userId)
    {
        return $this->userRepository->find($userId);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->userRepository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findByUsername($username)
    {
        return $this->findOneBy(array('username' => $username));
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->userRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, $order = array())
    {
        return $this->userRepository->findOneBy($criteria, $order);
    }

    /**
     * {@inheritdoc}
     */
    public function countBy(array $criteria)
    {
        die(__METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function search($term)
    {
        $qb = $this->userRepository->createQueryBuilder('u');
        $qb
            ->where($qb->expr()->like('u.username', $qb->expr()->literal("%$term%")))
            ->orWhere($qb->expr()->like('u.email', $qb->expr()->literal("%$term%")))
            ->orWhere($qb->expr()->like('u.firstname', $qb->expr()->literal("%$term%")))
            ->orWhere($qb->expr()->like('u.lastname', $qb->expr()->literal("%$term%")));

        return $qb->getQuery()->getResult();
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
        $qb = $this->userRepository->createQueryBuilder('u');
        $qb->where($qb->expr()->gte('u.modifiedAt', $qb->expr()->literal(date('Y-m-d H:i:s'))));

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function checkUsername($username)
    {
        return $this->findOneBy(array('username' => $username)) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function checkEmail($email)
    {
        return $this->findOneBy(array('email' => $email)) ? true : false;
    }

    /**
     * {@inheritDoc}
     */
    public function updatePassword(UserInterface $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $encoder = $this->encoderFactory->getEncoder($user);
            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
            $user->eraseCredentials();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateUser(UserInterface $user)
    {
        $this->updatePassword($user);

        $isUpdate = false;
        if ($this->entityManager->contains($user)) {
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

        $this->entityManager->persist($user);
        $this->entityManager->flush($user);

        $event = new UserEvent($user);
        if ($isUpdate) {
            $this->dispatcher->dispatch(UserEvents::UPDATE_USER, $event);
        } else {
            $this->dispatcher->dispatch(UserEvents::CREATE_USER, $event);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reloadUser(UserInterface $user)
    {
        $this->entityManager->refresh($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->findByUsername($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $reloadedUser = $this->find($user->getId());

        if (!$reloadedUser) {
            throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        $userClass = $this->getUserClass();

        return $class === $userClass || is_subclass_of($class, $userClass);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteUser(UserInterface $user, UserInterface $successorUser)
    {
        $successor = new SuccessorService();
        $successor->setSuccessor($user, $successorUser);

        $event = new UserEvent($user);
        if ($this->dispatcher->dispatch(UserEvents::BEFORE_DELETE_USER, $event)->isPropagationStopped()) {
            return;
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush($user);

        $event = new UserEvent($user);
        $this->dispatcher->dispatch(UserEvents::DELETE_USER, $event);
    }
}
