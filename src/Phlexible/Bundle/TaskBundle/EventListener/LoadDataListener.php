<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\EventListener;

use Phlexible\Bundle\ElementBundle\Event\LoadDataEvent;
use Phlexible\Bundle\TaskBundle\Entity\Task;
use Phlexible\Bundle\TaskBundle\Model\TaskManagerInterface;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Load data listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LoadDataListener
{
    /**
     * @var TaskManagerInterface
     */
    private $taskManager;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param TaskManagerInterface     $taskManager
     * @param UserManagerInterface     $userManager
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(
        TaskManagerInterface $taskManager,
        UserManagerInterface $userManager,
        SecurityContextInterface $securityContext
    )
    {
        $this->taskManager = $taskManager;
        $this->userManager = $userManager;
        $this->securityContext = $securityContext;
    }

    public function onLoadData(LoadDataEvent $event)
    {
        if ($teaser = $event->getTeaser()) {
            $taskPayload = array('teaser_id' => $teaser->getId(), 'language' => $event->getLanguage());
        } else {
            $taskPayload = array('tid' => $event->getNode()->getId(), 'language' => $event->getLanguage());
        }

        // TODO: repair
        return;

        $task = $this->taskManager->findOneByPayload(
            $taskPayload,
            null,
            array(
                Task::STATUS_OPEN,
                Task::STATUS_REJECTED,
                Task::STATUS_REOPENED,
                Task::STATUS_FINISHED
            )
        );

        if (!$task) {
            unset($taskPayload['language']);

            $task = $this->taskManager->findOneByPayload(
                $taskPayload,
                null,
                array(
                    Task::STATUS_OPEN,
                    Task::STATUS_REJECTED,
                    Task::STATUS_REOPENED,
                    Task::STATUS_FINISHED
                )
            );
        }

        if (!$task) {
            return;
        }

        /* @var $task Task */

        $latestStatus = $task->getLatestStatus();
        $createUserId = $task->getCreateUserId();
        $recipientUserId = $task->getRecipientUserId();
        $currentUserId = $this->securityContext->getToken()->getUser()->getId();

        $taskInfo = array(
            'id'        => $task->getId(),
            'status'    => $latestStatus->getStatus(),
            'type'      => $task->getCreateUserId() === $currentUserId
                ? 1
                : ($task->getRecipientUserId() === $currentUserId ? 2 : 3),
            'generic'   => $task->isGeneric() ? 1 : 0,
            'text'      => $task->getTitle(),
            'creator'   => $this->userManager->find($createUserId)->getDisplayName(),
            'recipient' => $this->userManager->find($recipientUserId)->getDisplayName(),
            'date'      => $task->getCreatedAt()->format('Y-m-d'),
            'time'      => $task->getCreatedAt()->format('H:i:s'),
        );

        $event->getData()->task = $taskInfo;
    }
}