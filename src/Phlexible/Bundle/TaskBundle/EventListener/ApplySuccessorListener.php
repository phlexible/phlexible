<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\UserBundle\Event\ApplySuccessorEvent;

/**
 * Tasks callbacks
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ApplySuccessorListener
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param ApplySuccessorEvent $event
     */
    public function onApplySuccessor(ApplySuccessorEvent $event)
    {
        $fromUser = $event->getFromUser();
        $toUser   = $event->getToUser();

        $fromUserId = $fromUser->getId();
        $toUserId   = $toUser->getId();

        $taskRepository = $this->entityManager->getRepository('PhlexibleTaskBundle:Task');

        foreach ($taskRepository->findByCreateUserId($fromUserId) as $task) {
            $task->setCreateUserId($toUserId);
        }

        foreach ($taskRepository->findByRecipientUserId($fromUserId) as $task) {
            $task->setRecipientUserId($toUserId);
        }

        $this->entityManager->flush();
    }
}