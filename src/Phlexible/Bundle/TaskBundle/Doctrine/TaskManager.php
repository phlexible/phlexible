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
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Phlexible\Bundle\TaskBundle\Entity\Status;
use Phlexible\Bundle\TaskBundle\Entity\Task;
use Phlexible\Bundle\TaskBundle\Mailer\Mailer;
use Phlexible\Bundle\TaskBundle\Model\TaskManagerInterface;
use Phlexible\Bundle\TaskBundle\Task\Type\TypeCollection;
use Phlexible\Bundle\TaskBundle\Task\Type\TypeInterface;
use Phlexible\Bundle\TaskBundle\TasksMessage;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;

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
     * @param EntityManager        $entityManager
     * @param TypeCollection       $types
     * @param UserManagerInterface $userManager
     * @param MessagePoster        $messageService
     * @param Mailer               $mailer
     * @param bool                 $sendMailOnClose
     */
    public function __construct(
        EntityManager $entityManager,
        TypeCollection $types,
        UserManagerInterface $userManager,
        MessagePoster $messageService,
        Mailer $mailer,
        $sendMailOnClose)
    {
        $this->entityManager = $entityManager;
        $this->types = $types;
        $this->userManager = $userManager;
        $this->messageService = $messageService;
        $this->mailer = $mailer;
        $this->sendMailOnClose = $sendMailOnClose;

        $this->taskRepository = $entityManager->getRepository('PhlexibleTaskBundle:Task');
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->taskRepository->find($id);
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
        $qb = $this->taskRepository->createQueryBuilder('t');
        $qb->where($qb->expr()->eq('t.createUserId', $qb->expr()->literal($userId)));
        $qb->where($qb->expr()->in('t.currentStatus', $status));

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
        $qb = $this->taskRepository->createQueryBuilder('t');
        $qb->select('COUNT(t.id)');
        $qb->where($qb->expr()->eq('t.createUserId', $qb->expr()->literal($userId)));
        $qb->where($qb->expr()->in('t.currentStatus', $status));

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
        $qb = $this->taskRepository->createQueryBuilder('t');
        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->eq('t.createUserId', $qb->expr()->literal($userId)),
                    $qb->expr()->IN(
                        't.currentStatus',
                        array(TASK::STATUS_REJECTED, TASK::STATUS_FINISHED, TASK::STATUS_CLOSED)
                    )
                ),
                $qb->expr()->andX(
                    $qb->expr()->eq('t.recipientUserId', $qb->expr()->literal($userId)),
                    $qb->expr()->IN('t.currentStatus', array(Task::STATUS_OPEN, Task::STATUS_REOPENED))
                )
            )
        );
        $qb->where($qb->expr()->in('t.currentStatus', $status));

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
        $qb = $this->taskRepository->createQueryBuilder('t');
        $qb->select('COUNT(t.id)');
        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->eq('t.createUserId', $qb->expr()->literal($userId)),
                    $qb->expr()->IN(
                        't.currentStatus',
                        array(TASK::STATUS_REJECTED, TASK::STATUS_FINISHED, TASK::STATUS_CLOSED)
                    )
                ),
                $qb->expr()->andX(
                    $qb->expr()->eq('t.recipientUserId', $qb->expr()->literal($userId)),
                    $qb->expr()->IN('t.currentStatus', array(Task::STATUS_OPEN, Task::STATUS_REOPENED))
                )
            )
        );
        $qb->where($qb->expr()->in('t.currentStatus', $status));

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
        $qb = $this->taskRepository->createQueryBuilder('t');
        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->eq('t.createUserId', $qb->expr()->literal($userId)),
                $qb->expr()->eq('t.recipientUserId', $qb->expr()->literal($userId))
            )
        );
        $qb->where($qb->expr()->in('t.currentStatus', $status));

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
        $qb = $this->taskRepository->createQueryBuilder('t');
        $qb->select('COUNT(t.id)');
        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->eq('t.createUserId', $qb->expr()->literal($userId)),
                $qb->expr()->eq('t.recipientUserId', $qb->expr()->literal($userId))
            )
        );
        $qb->where($qb->expr()->in('t.currentStatus', $status));

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByStatus(array $status = array(), array $sort = array(), $limit = null, $start = null)
    {
        $qb = $this->taskRepository->createQueryBuilder('t');
        $qb->where($qb->expr()->in('t.currentStatus', $status));

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
        $qb = $this->taskRepository->createQueryBuilder('t');
        $qb->select('COUNT(t.id)');
        $qb->where($qb->expr()->in('t.currentStatus', $status));

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function createTask(TypeInterface $type, $createUserId, $recipientUserId, array $payload, $comment)
    {
        $status = Task::STATUS_OPEN;

        $task = new Task();
        $task
            ->setCreateUserId($createUserId)
            ->setCreatedAt(new \DateTime())
            ->setCurrentStatus($status)
            ->setPayload($payload)
            ->setRecipientUserId($recipientUserId)
            ->setType($type->getName());

        $taskStatus = new Status();
        $taskStatus
            ->setTask($task)
            ->setCreateUserId($createUserId)
            ->setCreatedAt(new \DateTime())
            ->setComment($comment)
            ->setStatus($status);

        $this->entityManager->persist($task);
        $this->entityManager->persist($taskStatus);
        $this->entityManager->flush();

        $createUser = $this->userManager->find($createUserId);
        $recipientUser = $this->userManager->find($recipientUserId);

        $body = 'Task created by ' . $createUser->getDisplayName() .
            ' for ' . $recipientUser->getDisplayName() . PHP_EOL .
            'Comment: ' . PHP_EOL . $comment . PHP_EOL;

        $message = TasksMessage::create("Task set to status $status", $body);
        $this->messageService->post($message);

        $this->mailer->sendNewTaskEmailMessage($createUser, $recipientUser, $taskStatus, $type);

        return $task;
    }

    /**
     * {@inheritdoc}
     */
    public function createStatus(Task $task, $userId, $comment, $newStatus = Task::STATUS_OPEN)
    {
        $task->setCurrentStatus($newStatus);

        if ($newStatus === Task::STATUS_CLOSED) {
            $task->setClosedAt(new \DateTime());
        }

        $taskStatus = new Status();
        $taskStatus
            ->setTask($task)
            ->setComment($comment)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($userId)
            ->setStatus($newStatus);
        $this->entityManager->persist($taskStatus);
        $this->entityManager->flush();

        if ($userId == $task->getRecipientUserId()) {
            $fromUser = $this->userManager->find($task->getCreateUserId());
            $toUser = $this->userManager->find($task->getRecipientUserId());
        } else {
            $fromUser = $this->userManager->find($task->getRecipientUserId());
            $toUser = $this->userManager->find($task->getCreateUserId());
        }

        $body = 'Task status changed by ' . $fromUser->getDisplayName() .
            ' for ' . $toUser->getDisplayName() . PHP_EOL .
            'Comment: ' . PHP_EOL . $comment . PHP_EOL;

        $message = TasksMessage::create("Task set to status $newStatus", $body);
        $this->messageService->post($message);

        if ($newStatus !== Task::STATUS_CLOSED || $this->sendMailOnClose) {
            $type = $this->types->get($task->getType());

            $this->mailer->sendNewStatusEmailMessage($fromUser, $toUser, $taskStatus, $type);
        }

        return $taskStatus;
    }
}
