<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Finite\StateMachine\StateMachineInterface;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Phlexible\Bundle\TaskBundle\Entity\Comment;
use Phlexible\Bundle\TaskBundle\Entity\Task;
use Phlexible\Bundle\TaskBundle\Entity\Transition;
use Phlexible\Bundle\TaskBundle\Mailer\Mailer;
use Phlexible\Bundle\TaskBundle\Model\TaskManagerInterface;
use Phlexible\Bundle\TaskBundle\Task\Type\TypeCollection;
use Phlexible\Bundle\TaskBundle\Task\Type\TypeInterface;
use Phlexible\Bundle\TaskBundle\TasksMessage;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Task manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TaskManager implements TaskManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $taskRepository;

    /**
     * @var TypeCollection
     */
    private $types;

    /**
     * @var StateMachineInterface
     */
    private $stateMachine;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var MessagePoster
     */
    private $messageService;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var bool
     */
    private $sendMailOnClose;

    /**
     * @param EntityManager         $entityManager
     * @param TypeCollection        $types
     * @param StateMachineInterface $stateMachine
     * @param UserManagerInterface  $userManager
     * @param MessagePoster         $messageService
     * @param Mailer                $mailer
     * @param bool                  $sendMailOnClose
     */
    public function __construct(
        EntityManager $entityManager,
        TypeCollection $types,
        StateMachineInterface $stateMachine,
        UserManagerInterface $userManager,
        MessagePoster $messageService,
        Mailer $mailer,
        $sendMailOnClose)
    {
        $this->entityManager = $entityManager;
        $this->types = $types;
        $this->stateMachine = $stateMachine;
        $this->userManager = $userManager;
        $this->messageService = $messageService;
        $this->mailer = $mailer;
        $this->sendMailOnClose = $sendMailOnClose;
    }

    /**
     * @return EntityRepository
     */
    private function getTaskRepository()
    {
        if (null === $this->taskRepository) {
            $this->taskRepository = $this->entityManager->getRepository('PhlexibleTaskBundle:Task');
        }

        return $this->taskRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getTaskRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getTaskRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findByCreatedByAndStatus(
        $userId,
        array $status = array(),
        array $sort = array(),
        $limit = null,
        $start = null)
    {
        $qb = $this->getTaskRepository()->createQueryBuilder('t');
        $qb
            ->where($qb->expr()->eq('t.createUserId', $qb->expr()->literal($userId)))
            ->where($qb->expr()->in('t.finiteState', $status));

        foreach ($sort as $field => $dir) {
            $qb->orderBy("t.$field", $dir);
        }
        if ($start) {
            $qb->setFirstResult($start);
        }
        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countByCreatedByAndStatus($userId, array $status = array())
    {
        $qb = $this->getTaskRepository()->createQueryBuilder('t');
        $qb
            ->select('COUNT(t.id)')
            ->where($qb->expr()->eq('t.createUserId', $qb->expr()->literal($userId)))
            ->where($qb->expr()->in('t.finiteState', $status));

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByAssignedToAndStatus(
        $userId,
        array $status = array(),
        array $sort = array(),
        $limit = null,
        $start = null)
    {
        $qb = $this->getTaskRepository()->createQueryBuilder('t');
        $qb
            ->where($qb->expr()->eq('t.assignedUserId', $qb->expr()->literal($userId)));

        if ($status) {
            $qb->andWhere($qb->expr()->IN('t.finiteState', $status));
        }

        foreach ($sort as $field => $dir) {
            $qb->orderBy("t.$field", $dir);
        }
        if ($start) {
            $qb->setFirstResult($start);
        }
        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countByAssignedToAndStatus($userId, array $status = array())
    {
        $qb = $this->getTaskRepository()->createQueryBuilder('t');
        $qb
            ->select('COUNT(t.id)')
            ->where($qb->expr()->eq('t.assignedUserId', $qb->expr()->literal($userId)));

        if ($status) {
            $qb->andWhere($qb->expr()->IN('t.finiteState', $status));
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByInvolvementAndStatus(
        $userId,
        array $status = array(),
        array $sort = array(),
        $limit = null,
        $start = null)
    {
        $qb = $this->getTaskRepository()->createQueryBuilder('t');
        $qb
            ->where($qb->expr()->like('t.involedUserIds', $qb->expr()->literal("%$userId%")))
            ->where($qb->expr()->in('t.finiteState', $status));

        foreach ($sort as $field => $dir) {
            $qb->orderBy("t.$field", $dir);
        }
        if ($start) {
            $qb->setFirstResult($start);
        }
        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countByInvolvementAndStatus($userId, array $status = array())
    {
        $qb = $this->getTaskRepository()->createQueryBuilder('t');
        $qb
            ->select('COUNT(t.id)')
            ->where($qb->expr()->like('t.involedUserIds', $qb->expr()->literal("%$userId%")))
            ->where($qb->expr()->in('t.finiteState', $status));

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByStatus(array $status = array(), array $sort = array(), $limit = null, $start = null)
    {
        $qb = $this->getTaskRepository()->createQueryBuilder('t');

        if ($status) {
            $qb->where($qb->expr()->in('t.finiteState', $status));
        }

        foreach ($sort as $field => $dir) {
            $qb->orderBy("t.$field", $dir);
        }
        if ($start) {
            $qb->setFirstResult($start);
        }
        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countByStatus(array $status = array())
    {
        $qb = $this->getTaskRepository()->createQueryBuilder('t');
        $qb
            ->select('COUNT(t.id)')
            ->where($qb->expr()->in('t.finiteState', $status));

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByPayload(array $payload)
    {
        ksort($payload);

        $payload = json_encode($payload);

        return $this->getTaskRepository()->findOneBy(array('payload' => $payload));
    }

    /**
     * {@inheritdoc}
     */
    public function getTransitions(Task $task)
    {
        $this->stateMachine->setObject($task);
        $this->stateMachine->initialize();

        return $this->stateMachine->getCurrentState()->getTransitions();
    }

    /**
     * {@inheritdoc}
     */
    public function createTask(TypeInterface $type, UserInterface $createUser, UserInterface $assignedUser, array $payload, $description)
    {
        $task = new Task();
        $task
            ->setCreateUserId($createUser->getId())
            ->setCreatedAt(new \DateTime())
            ->setDescription($description)
            ->setFiniteState(Task::STATUS_OPEN)
            ->setPayload($payload)
            ->setAssignedUserId($assignedUser->getId())
            ->setType($type->getName());

        $this->stateMachine->setObject($task);
        $this->stateMachine->initialize();

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $body = 'New task created by ' . $createUser->getDisplayName() .
            ', assigned to ' . $assignedUser->getDisplayName() . PHP_EOL .
            'Description: ' . PHP_EOL . $description . PHP_EOL;

        $message = TasksMessage::create("New task", $body);
        $this->messageService->post($message);

        if ($createUser !== $assignedUser) {
            $this->mailer->sendNewTaskEmailMessage($task, $createUser, $assignedUser, $type);
        }

        return $task;
    }

    /**
     * {@inheritdoc}
     */
    public function updateTask(Task $task, UserInterface $byUser, $transitionName = null, UserInterface $assignUser = null, $comment = null)
    {
        $changes = array();

        if ($transitionName) {
            $oldState = $task->getFiniteState();

            $this->stateMachine->setObject($task);
            $this->stateMachine->initialize();
            $this->stateMachine->apply($transitionName);

            $taskTransition = new Transition();
            $taskTransition
                ->setTask($task)
                ->setName($transitionName)
                ->setOldState($oldState)
                ->setNewState($task->getFiniteState())
                ->setCreatedAt(new \DateTime())
                ->setCreateUserId($byUser->getId())
            ;

            $this->entityManager->persist($taskTransition);

            $changes['transition'] = $taskTransition;
        }

        if ($assignUser) {
            $task->setAssignedUserId($assignUser->getId());

            $changes['assign'] = $assignUser;
        }

        if ($comment) {
            $taskComment = new Comment();
            $taskComment
                ->setTask($task)
                ->setComment($comment)
                ->setCreatedAt(new \DateTime())
                ->setCreateUserId($byUser->getId())
                ->setCurrentState($task->getFiniteState());

            $this->entityManager->persist($taskComment);

            $changes['comment'] = $taskComment;
        }

        $body = 'Task updated:' . PHP_EOL;
        if (isset($changes['transition'])) {
            $body .= "Status changed from {$changes['transition']->getOldState()} to {$changes['transition']->getNewState()}" . PHP_EOL;
        }
        if (isset($changes['assign'])) {
            $body .= "Assigned to {$changes['assign']->getDisplayName()}" . PHP_EOL;
        }
        if (isset($changes['comment'])) {
            $body .= 'Comment:' . PHP_EOL . $changes['comment']->getComment() . PHP_EOL;
        }

        $message = TasksMessage::create("Task updated", $body);
        $this->messageService->post($message);

        $type = $this->types->get($task->getType());

        $involvedUsers = array();
        foreach ($task->getInvolvedUserIds() as $involvedUserId) {
            $involvedUser = $this->userManager->find($involvedUserId);
            if ($involvedUser) {
                $involvedUsers[] = $involvedUser;
            }
        }

        $this->mailer->sendUpdateEmailMessage($task, $byUser, $involvedUsers, $changes, $type);
    }
}
