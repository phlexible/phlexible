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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\UserBundle\Entity\Group;
use Phlexible\Bundle\UserBundle\Event\GroupEvent;
use Phlexible\Bundle\UserBundle\Model\GroupManagerInterface;
use Phlexible\Bundle\UserBundle\UserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Group manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GroupManager implements GroupManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $groupRepository;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var string
     */
    private $everyoneGroupId;

    /**
     * @var string
     */
    private $groupClass;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $dispatcher
     * @param string                   $groupClass
     * @param string                   $everyoneGroupId
     */
    public function __construct(EntityManager $entityManager,
                                EventDispatcherInterface $dispatcher,
                                $groupClass,
                                $everyoneGroupId)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->everyoneGroupId = $everyoneGroupId;

        $this->groupClass = $groupClass;
        $this->groupRepository = $entityManager->getRepository($this->groupClass);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupClass()
    {
        return $this->groupClass;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $groupClass = $this->getGroupClass();

        return new $groupClass();
    }

    /**
     * {@inheritdoc}
     */
    public function find($groupId)
    {
        return $this->groupRepository->find($groupId);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->groupRepository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($name)
    {
        return $this->groupRepository->findOneBy(['name' => $name]);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->groupRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, $order = [])
    {
        return $this->groupRepository->findOneBy($criteria, $order);
    }

    /**
     * {@inheritdoc}
     */
    public function getEveryoneGroupId()
    {
        return $this->everyoneGroupId;
    }

    /**
     * {@inheritdoc}
     */
    public function checkName($name)
    {
        return $this->findOneBy(['name' => $name]) ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function updateGroup(Group $group)
    {
        $isUpdate = false;
        if ($this->entityManager->contains($group)) {
            $isUpdate = true;
        }

        $event = new GroupEvent($group);
        if ($isUpdate) {
            $this->dispatcher->dispatch(UserEvents::BEFORE_UPDATE_GROUP, $event);
        } else {
            $this->dispatcher->dispatch(UserEvents::BEFORE_CREATE_GROUP, $event);
        }
        if ($event->isPropagationStopped()) {
            return;
        }

        $this->entityManager->persist($group);
        $this->entityManager->flush($group);

        $event = new GroupEvent($group);
        if ($isUpdate) {
            $this->dispatcher->dispatch(UserEvents::UPDATE_GROUP, $event);
        } else {
            $this->dispatcher->dispatch(UserEvents::CREATE_GROUP, $event);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reloadGroup(Group $group)
    {
        $this->entityManager->refresh($group);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteGroup(Group $group)
    {
        $event = new GroupEvent($group);
        if ($this->dispatcher->dispatch(UserEvents::BEFORE_DELETE_GROUP, $event)->isPropagationStopped()) {
            return;
        }

        $this->entityManager->remove($group);
        $this->entityManager->flush($group);

        $event = new GroupEvent($group);
        $this->dispatcher->dispatch(UserEvents::DELETE_USER, $event);
    }
}
