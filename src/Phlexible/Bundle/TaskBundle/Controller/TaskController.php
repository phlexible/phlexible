<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\TaskBundle\Entity\Task;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Task controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/tasks")
 * @Security("is_granted('tasks')")
 */
class TaskController extends Controller
{
    /**
     * List tasks
     *
     * @param Request $request
     *
     * @throws \Exception
     * @return JsonResponse
     * @Route("/list", name="tasks_list")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="Search",
     *   requirements={
     *     {"name"="query", "dataType"="string", "required"=true, "description"="Search query"}
     *   },
     *   filters={
     *     {"name"="limit", "dataType"="integer", "default"=20, "description"="Limit results"},
     *     {"name"="start", "dataType"="integer", "default"=0, "description"="Result offset"},
     *     {"name"="sort", "dataType"="string", "default"="created_at", "description"="Sort field"},
     *     {"name"="dir", "dataType"="string", "default"="DESC", "description"="Sort direction"},
     *     {"name"="tasks", "dataType"="string", "default"="involved", "description"="involvement"},
     *     {"name"="status_open", "dataType"="boolean", "default"=false, "description"="Status open"},
     *     {"name"="status_rejected", "dataType"="boolean", "default"=false, "description"="Status rejected"},
     *     {"name"="status_reopened", "dataType"="boolean", "default"=false, "description"="Status reopened"},
     *     {"name"="status_finished", "dataType"="boolean", "default"=false, "description"="Status finished"},
     *     {"name"="status_closed", "dataType"="boolean", "default"=false, "description"="Status closed"}
     *   }
     * )
     */
    public function listAction(Request $request)
    {
        $type = $request->request->get('tasks', 'involved');
        $sort = $request->request->get('sort', 'createdAt');
        $dir = $request->request->get('dir', 'DESC');
        $limit = $request->request->get('limit', 20);
        $start = $request->request->get('start', 0);

        $status = array();
        if ($request->request->get('status_open')) {
            $status[] = Task::STATUS_OPEN;
        }
        if ($request->request->get('status_rejected')) {
            $status[] = Task::STATUS_REJECTED;
        }
        if ($request->request->get('status_reopened')) {
            $status[] = Task::STATUS_REOPENED;
        }
        if ($request->request->get('status_finished')) {
            $status[] = Task::STATUS_FINISHED;
        }
        if ($request->request->get('status_closed')) {
            $status[] = Task::STATUS_CLOSED;
        }
        if (!count($status)) {
            $status[] = Task::STATUS_OPEN;
        }

        $taskManager = $this->get('phlexible_task.task_manager');
        /* @var $taskRepository EntityRepository */
        $userManager = $this->get('phlexible_user.user_manager');
        $types = $this->get('phlexible_task.types');

        $userId = $this->getUser()->getId();

        switch ($type) {
            case 'tasks':
                $tasks = $taskManager->findByCreatedByAndStatus($userId, $status, array($sort => $dir), $limit, $start);
                $total = $taskManager->countByCreatedByAndStatus($userId, $status);
                break;

            case 'todos':
                $tasks = $taskManager->findByAssignedToAndStatus($userId, $status, array($sort => $dir), $limit, $start);
                $total = $taskManager->countByAssignedToAndStatus($userId, $status);
                break;

            case 'involved':
                $tasks = $taskManager->findByInvolvementAndStatus($userId, $status, array($sort => $dir), $limit, $start);
                $total = $taskManager->countByInvolvementAndStatus($userId, $status);
                break;

            case 'all':
            default:
                $tasks = $taskManager->findByStatus($status, array($sort => $dir), $limit, $start);
                $total = $taskManager->countByStatus($status);
                break;
        }

        $data = array();
        foreach ($tasks as $task) {
            $currentStatus = $task->getCurrentStatus();

            if (in_array($currentStatus, array(Task::STATUS_OPEN, Task::STATUS_REOPENED, Task::STATUS_CLOSED))) {
                $assignedUser = $userManager->find($task->getRecipientUserId());
            } elseif (in_array(
                $currentStatus,
                array(Task::STATUS_REJECTED, Task::STATUS_FINISHED, Task::STATUS_CLOSED)
            )
            ) {
                $assignedUser = $userManager->find($task->getCreateUserId());
            } else {
                throw new \Exception('Unknown status.');
            }

            $createUser = $userManager->find($task->getCreateUserId());
            // $recipientUser = $task->getRecipientUser();
            $latestStatus = $task->getLatestStatus();
            $latestUser = $userManager->find($latestStatus->getCreateUserId());

            $historyItems = $task->getStatus();

            $history = array();
            foreach ($historyItems as $historyItem) {
                $user = $userManager->find($historyItem->getCreateUserId());
                $history[] = array(
                    'create_date' => $historyItem->getCreatedAt()->format('Y-m-d H:i:s'),
                    'name'        => $user->getDisplayName(),
                    'status'      => $historyItem->getStatus(),
                    'comment'     => $historyItem->getComment(),
                    'latest'      => $latestStatus->getId() == $historyItem->getId(),
                );
            }
            $history = array_reverse($history);

            $type = $types->get($task->getType());

            $data[] = array(
                'id'             => $task->getId(),
                'type'           => $task->getType(),
                'generic'        => $task->getType() === 'generic',
                'title'          => $type->getTitle($task),
                'text'           => $type->getText($task),
                'component'      => $type->getComponent(),
                'link'           => $type->getLink($task),
                'created'        => $task->getCreateUserId() === $this->getUser()->getId() ? 1 : 0,
                'received'       => $task->getRecipientUserId() === $this->getUser()->getId() ? 1 : 0,
                'assigned_user'  => $assignedUser->getFirstname() . ' ' . $assignedUser->getLastname(),
                'latest_status'  => $latestStatus->getStatus(),
                'latest_comment' => $latestStatus->getComment(),
                'latest_user'    => $latestUser->getFirstname() . ' ' . $latestUser->getLastname(),
                'latest_uid'     => $latestStatus->getCreateUserId(),
                'latest_date'    => $latestStatus->getCreatedAt()->format('Y-m-d H:i:s'),
                'create_user'    => $createUser->getFirstname() . ' ' . $createUser->getLastname(),
                'create_uid'     => $task->getCreateUserId(),
                'create_date'    => $task->getCreatedAt()->format('Y-m-d H:i:s'),
                'latest_id'      => $latestStatus->getId(),
                'history'        => $history,
            );
        }

        return new JsonResponse(array(
            'tasks' => $data,
            'total' => $total,
        ));
    }

    /**
     * List types
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/types", name="tasks_types")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="List task types",
     *   filters={
     *     {"name"="component", "dataType"="string", "description"="Component filter"}
     *   }
     * )
     */
    public function typesAction(Request $request)
    {
        $component = $request->request->get('component');

        $types = $this->get('phlexible_task.types');

        $tasks = array();
        foreach ($types as $type) {
            if ($component && $type->getComponent() !== $component) {
                continue;
            }

            $tasks[] = array(
                'id'   => $type,
                'task' => $type,
            );
        }

        return new JsonResponse(array('tasks' => $tasks));
    }

    /**
     * List recipients
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/recipients", name="tasks_recipients")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="List recipients"
     * )
     */
    public function recipientsAction(Request $request)
    {
        $taskType = $request->request->get('task_type');

        $types = $this->get('phlexible_task.types');
        $userManager = $this->get('phlexible_user.user_manager');

        $systemUserId = $userManager->getSystemUserId();

        $type = $types->get($taskType);

        $users = array();
        foreach ($userManager->findAll() as $user) {
            if ($user->getId() === $systemUserId) {
                continue;
            }

            if (!$user->isGranted('tasks')) {
                continue;
            }

            if ($type->getResources() && !$user->isAllowed($type->getResources())) {
                continue;
            }

            $users[$user->getDisplayName()] = array(
                'uid'      => $user->getId(),
                'username' => $user->getDisplayName(),
            );
        }

        ksort($users);
        $users = array_values($users);

        return new JsonResponse(array('users' => $users));
    }

    /**
     * Create task
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create/task", name="tasks_create_task")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="Create task",
     *   requirements={
     *     {"name"="type", "dataType"="string", "required"=true, "description"="Task type"},
     *     {"name"="recipient", "dataType"="string", "required"=true, "description"="Recipient"},
     *     {"name"="comment", "dataType"="string", "required"=true, "description"="Comment"},
     *     {"name"="payload", "dataType"="array", "required"=true, "description"="Payload"}
     *   }
     * )
     */
    public function createtaskAction(Request $request)
    {
        $taskType = $request->request->get('type');
        $recipientUserId = $request->request->get('recipient');
        $comment = $request->request->get('comment');
        $payload = $request->request->get('payload');

        if ($payload) {
            $payload = json_decode($payload, true);
        }

        $taskManager = $this->get('phlexible_task.task_manager');
        $types = $this->get('phlexible_task.types');
        $type = $types->get($taskType);

        $task = $taskManager->createTask($type, $this->getUser()->getId(), $recipientUserId, $payload, $comment);

        return new ResultResponse(true, 'Task created.');
    }

    /**
     * Create task status
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create/status", name="tasks_create_status")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="Create status",
     *   requirements={
     *     {"name"="task_id", "dataType"="string", "required"=true, "description"="Task ID"},
     *     {"name"="new_status", "dataType"="string", "required"=true, "description"="New status"},
     *     {"name"="comment", "dataType"="string", "required"=true, "description"="Comment"}
     *   }
     * )
     */
    public function createstatusAction(Request $request)
    {
        $taskId = $request->request->get('task_id');
        $newStatus = $request->request->get('new_status');
        $comment = $request->request->get('comment');

        if ($comment) {
            $comment = urldecode($comment);
        }

        $taskRepository = $this->getDoctrine()->getRepository('PhlexibleTaskBundle:Task');
        $taskManager = $this->get('phlexible_task.task_manager');

        $task = $taskRepository->find($taskId);
        $taskManager->createStatus($task, $this->getUser()->getId(), $comment, $newStatus);

        return new ResultResponse(true, 'Task status created.');
    }

    /**
     * View task
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/view", name="tasks_view")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="View",
     *   requirements={
     *     {"name"="task_id", "dataType"="string", "required"=true, "description"="Task ID"}
     *   }
     * )
     */
    public function viewAction(Request $request)
    {
        $taskId = $request->request->get('task_id');

        $taskManager = $this->get('phlexible_task.task_manager');
        $types = $this->get('phlexible_task.types');
        $userManager = $this->get('phlexible_user.user_manager');

        $task = $taskManager->find($taskId);
        $latestStatus = $task->getLatestStatus();

        $createUser = $userManager->find($task->getCreateUserId());
        $recipientUser = $userManager->find($task->getRecipientUserId());
        $latestUser = $userManager->find($latestStatus->getCreateUserId());

        if (in_array($latestStatus->getStatus(), array(Task::STATUS_OPEN, Task::STATUS_REOPENED))) {
            $assignedUser = $recipientUser;
        } else {
            $assignedUser = $createUser;
        }

        $historyItems = $task->getStatus();

        $history = array();
        foreach ($historyItems as $historyItem) {
            $user = $userManager->find($historyItem->getCreateUserId());
            $history[] = array(
                'create_date' => $historyItem->getCreatedAt()->format('Y-m-d H:i:s'),
                'name'        => $user->getDisplayName(),
                'status'      => $historyItem->getStatus(),
                'comment'     => $historyItem->getComment(),
                'latest'      => $latestStatus->getId() === $historyItem->getId(),
            );
        }
        $history = array_reverse($history);

        $type = $types->get($task->getType());

        $data = array(
            'id'             => $task->getId(),
            'type'           => $task->getType(),
            'title'          => $type->getTitle($task),
            'text'           => $type->getText($task),
            'component'      => $type->getComponent(),
            'created'        => $task->getCreateUserId() === $this->getUser()->getId() ? 1 : 0,
            'received'       => $task->getRecipientUserId() === $this->getUser()->getId() ? 1 : 0,
            'assigned_user'  => $assignedUser->getFirstname() . ' ' . $assignedUser->getLastname(),
            'latest_status'  => $latestStatus->getStatus(),
            'latest_comment' => $latestStatus->getComment(),
            'latest_user'    => $latestUser->getFirstname() . ' ' . $latestUser->getLastname(),
            'latest_uid'     => $latestStatus->getCreateUserId(),
            'latest_date'    => $latestStatus->getCreatedAt()->format('Y-m-d H:i:s'),
            'create_user'    => $createUser->getFirstname() . ' ' . $createUser->getLastname(),
            'create_uid'     => $task->getCreateUserId(),
            'create_date'    => $task->getCreatedAt()->format('Y-m-d H:i:s'),
            'recipient_user' => $recipientUser->getFirstname() . ' ' . $recipientUser->getLastname(),
            'recipient_uid'  => $task->getRecipientUserId(),
            'latest_id'      => $latestStatus->getId(),
            'history'        => $history,
        );

        return new JsonResponse($data);
    }
}
