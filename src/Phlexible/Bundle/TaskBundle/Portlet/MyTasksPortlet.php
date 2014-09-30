<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\Portlet;
use Phlexible\Bundle\TaskBundle\Entity\Task;
use Phlexible\Bundle\TaskBundle\Model\TaskManagerInterface;
use Phlexible\Bundle\TaskBundle\Task\Type\TypeCollection;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * My tasks portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MyTasksPortlet extends Portlet
{
    /**
     * @var TaskManagerInterface
     */
    private $taskManager;

    /**
     * @var TypeCollection
     */
    private $types;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var int
     */
    private $numItems;

    /**
     * @param TranslatorInterface      $translator
     * @param TaskManagerInterface     $taskManager
     * @param TypeCollection           $types
     * @param SecurityContextInterface $securityContext
     * @param UserManagerInterface     $userManager
     * @param int                      $numItems
     */
    public function __construct(TranslatorInterface $translator,
                                TaskManagerInterface $taskManager,
                                TypeCollection $types,
                                SecurityContextInterface $securityContext,
                                UserManagerInterface $userManager,
                                $numItems)
    {
        $this
            ->setId('my-tasks-portlet')
            ->setTitle($translator->trans('tasks.my_tasks', array(), 'gui'))
            //->setDescription('Displays your active tasks')
            ->setClass('Phlexible.tasks.portlet.MyTasks')
            ->setIconClass('p-task-portlet-icon');

        $this->taskManager = $taskManager;
        $this->types = $types;
        $this->securityContext = $securityContext;
        $this->userManager = $userManager;
        $this->numItems = $numItems;
    }

    /**
     * Return Portlet data
     *
     * @return array
     */
    public function getData()
    {
        $tasksToShow = $this->numItems;

        $tasks = $this->taskManager->findByAssignedToAndStatus(
            $this->securityContext->getToken()->getUser()->getId(),
            array(Task::STATUS_OPEN, Task::STATUS_REOPENED),
            array(),
            $tasksToShow
        );

        $data = array();

        foreach ($tasks as $task) {
            $latestStatus = $task->getLatestStatus();
            $createUser   = $this->userManager->find($latestStatus->getCreateUserId());
            $type         = $this->types->get($task);

            $data[] = array(
                'id'          => $task->getId(),
                'text'        => $type->getText($task),
                'type'        => $task->getType(),
                'status'      => $latestStatus->getStatus(),
                'comment'     => $latestStatus->getComment(),
                'create_user' => $createUser->getDisplayName(),
                'create_uid'  => $task->getCreateUserId(),
                'create_date' => $task->getCreatedAt()->format('Y-m-d H:i:s'),
            );
        }

        $tasksToShow -= count($data);

        if (!$tasksToShow) {
            return $data;
        }

        $tasks = $this->taskManager->findByAssignedToAndStatus(
            $this->securityContext->getToken()->getUser()->getId(),
            array(Task::STATUS_REJECTED, Task::STATUS_FINISHED),
            array(),
            $tasksToShow
        );

        foreach ($tasks as $task) {
            $latestStatus = $task->getLatestStatus();
            $createUser   = $this->userManager->find($latestStatus->getCreateUserId());
            $type         = $this->types->get($task);

            $data[] = array(
                'id'          => $task->getId(),
                'text'        => $type->getText($task),
                'type'        => $task->getType(),
                'status'      => $latestStatus->getStatus(),
                'comment'     => $latestStatus->getComment(),
                'create_user' => $createUser,
                'create_uid'  => $task->getCreateUserId(),
                'create_date' => $task->getCreatedAt()->format('Y-m-d H:i:s'),
            );
        }

        return $data;
    }
}
