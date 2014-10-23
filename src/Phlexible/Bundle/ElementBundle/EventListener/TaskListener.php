<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\TaskBundle\Entity\Task;
use Phlexible\Bundle\TaskBundle\Model\TaskManagerInterface;
use Phlexible\Bundle\TreeBundle\Event\NodeEvent;
use Phlexible\Bundle\TreeBundle\Event\PublishNodeEvent;
use Phlexible\Bundle\TreeBundle\Event\SetNodeOfflineEvent;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\TreeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Task listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TaskListener implements EventSubscriberInterface
{
    /**
     * @var TaskManagerInterface
     */
    private $taskManager;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param TaskManagerInterface     $taskManager
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(TaskManagerInterface $taskManager, SecurityContextInterface $securityContext)
    {
        $this->taskManager = $taskManager;
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            TreeEvents::PUBLISH_NODE => 'onPublishNode',
            TreeEvents::SET_NODE_OFFLINE => 'onSetNodeOffline',
            TreeEvents::DELETE_NODE => 'onDeleteNode',
        ];
    }

    /**
     * @param PublishNodeEvent $event
     */
    public function onPublishNode(PublishNodeEvent $event)
    {
        $node = $event->getNode();
        $language = $event->getLanguage();

        if ($node->getType() !== 'element') {
            return;
        }

        $this->doTask(
            [
                'type' => 'element',
                'type_id' => $node->getId(),
                'language' => $language
            ],
            'element.publish',
            $this->securityContext->getToken()->getUser()->getId()
        );
    }

    /**
     * @param SetNodeOfflineEvent $event
     */
    public function onSetNodeOffline(SetNodeOfflineEvent $event)
    {
        $node = $event->getNode();
        $language = $event->getLanguage();

        if ($node->getType() !== 'element') {
            return;
        }

        $this->doTask(
            [
                'type' => 'element',
                'type_id' => $node->getId(),
                'language' => $language
            ],
            'element.set_offline',
            $this->securityContext->getToken()->getUser()->getId()
        );
    }

    /**
     * @param NodeEvent $event
     */
    public function onDeleteNode(NodeEvent $event)
    {
        $node = $event->getNode();
        $language = null;

        if ($node->getType() !== 'element') {
            return;
        }

        $this->doTask(
            [
                'type' => 'element',
                'type_id' => $node->getId()
            ],
            'element.delete',
            $this->securityContext->getToken()->getUser()->getId()
        );
    }

    /**
     * @param array  $payload
     * @param string $type
     * @param string $userId
     */
    private function doTask(array $payload, $type, $userId)
    {
        $tasks = $this->taskManager->findBy(
            [
                'type' => $type,
                'finiteState' => [
                    Task::STATUS_OPEN,
                    Task::STATUS_REJECTED,
                    Task::STATUS_REOPENED,
                ]
            ]
        );

        if (!$tasks) {
            return;
        }

        ksort($payload);

        foreach ($tasks as $task) {
            /* @var $task Task */
            $taskPayload = $task->getPayload();
            ksort($taskPayload);

            if ($payload != $taskPayload) {
                continue;
            }

            $this->taskManager->createStatus($task, $userId, 'task done', Task::STATUS_FINISHED);
        }
    }
}
