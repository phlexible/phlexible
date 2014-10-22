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
use Phlexible\Bundle\TaskBundle\Task\Type\TypeInterface;
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
 * @Security("is_granted('ROLE_TASKS')")
 */
class TaskController extends Controller
{
    /**
     * List tasks
     *
     * @param Request $request
     *
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
            /* @var $task Task */
            $assignedUser = $userManager->find($task->getAssignedUserId());
            $createUser = $userManager->find($task->getCreateUserId());

            $transitions = array();
            foreach ($task->getTransitions() as $transition) {
                $transitionUser = $userManager->find($transition->getCreateUserId());
                $transitions[] = array(
                    'id'          => $transition->getId(),
                    'name'        => $transition->getName(),
                    'new_state'   => $transition->getNewState(),
                    'old_state'   => $transition->getOldState(),
                    'create_date' => $transition->getCreatedAt()->format('Y-m-d H:i:s'),
                    'create_user' => $transitionUser->getDisplayName(),
                );
            }

            $comments = array();
            foreach ($task->getComments() as $comment) {
                $commentUser = $userManager->find($comment->getCreateUserId());
                $comments[] = array(
                    'id'            => $comment->getId(),
                    'current_state' => $comment->getCurrentState(),
                    'comment'       => $comment->getComment(),
                    'create_date'   => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
                    'create_user'   => $commentUser->getDisplayName(),
                );
            }

            $type = $types->get($task->getType());

            $data[] = array(
                'id'             => $task->getId(),
                'type'           => $task->getType(),
                'generic'        => $task->getType() === 'generic',
                'title'          => $type->getTitle($task),
                'text'           => $type->getText($task),
                'description'    => $task->getDescription(),
                'component'      => $type->getComponent(),
                'link'           => $type->getLink($task),
                'assigned_user'  => $assignedUser->getDisplayName(),
                'status'         => $task->getFiniteState(),
                'create_user'    => $createUser->getDisplayName(),
                'create_uid'     => $task->getCreateUserId(),
                'create_date'    => $task->getCreatedAt()->format('Y-m-d H:i:s'),
                'transitions'    => $transitions,
                'comments'       => $comments,
                'states'         => $taskManager->getTransitions($task),
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

        $taskTypes = $this->get('phlexible_task.types');

        $types = array();
        foreach ($taskTypes->all() as $type) {
            /* @var $type TypeInterface */
            if ($component && $type->getComponent() !== $component) {
                continue;
            }

            $types[] = array(
                'id'   => $type->getName(),
                'name' => $type->getName(),
            );
        }

        return new JsonResponse(array('types' => $types));
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
     *   description="List recipients",
     *   requirements={
     *     {"name"="type", "dataType"="string", "required"=true, "description"="Task type"},
     *   }
     * )
     */
    public function recipientsAction(Request $request)
    {
        $taskType = $request->get('type');

        $types = $this->get('phlexible_task.types');
        $userManager = $this->get('phlexible_user.user_manager');
        $securityContext = $this->get('security.context');

        $systemUserId = $userManager->getSystemUserId();

        $type = $types->get($taskType);

        $users = array();
        foreach ($userManager->findAll() as $user) {
            if ($user->getId() === $systemUserId) {
                continue;
            }

            if (!$securityContext->isGranted('tasks')) {
                continue;
            }

            if ($type->getResource() && !$securityContext->isGranted($type->getResource())) {
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
     *     {"name"="description", "dataType"="string", "required"=true, "description"="Description"},
     *     {"name"="payload", "dataType"="array", "required"=true, "description"="Payload"}
     *   }
     * )
     */
    public function createTaskAction(Request $request)
    {
        $typeName = $request->get('type');
        $assignedUserId = $request->get('recipient');
        $description = $request->get('description');
        $payload = $request->get('payload');

        if ($payload) {
            $payload = json_decode($payload, true);
        }

        $taskManager = $this->get('phlexible_task.task_manager');
        $userManager = $this->get('phlexible_user.user_manager');
        $types = $this->get('phlexible_task.types');

        $type = $types->get($typeName);
        $assignedUser = $userManager->find($assignedUserId);

        $task = $taskManager->createTask($type, $this->getUser(), $assignedUser, $payload, $description);

        return new ResultResponse(true, 'Task created.');
    }

    /**
     * Create task comment
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create/comment", name="tasks_create_comment")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="Create status",
     *   requirements={
     *     {"name"="id", "dataType"="string", "required"=true, "description"="Task ID"},
     *     {"name"="comment", "dataType"="string", "required"=true, "description"="Comment"}
     *   }
     * )
     */
    public function commentAction(Request $request)
    {
        $id = $request->get('id');
        $comment = $request->get('comment');

        if ($comment) {
            $comment = urldecode($comment);
        }

        $taskManager = $this->get('phlexible_task.task_manager');

        $task = $taskManager->find($id);
        $taskManager->updateTask($task, $this->getUser(), $comment, null, $comment);

        return new ResultResponse(true, 'Task comment created.');
    }

    /**
     * Create task transition
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create/transition", name="tasks_create_transition")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="Create status",
     *   requirements={
     *     {"name"="id", "dataType"="string", "required"=true, "description"="Task ID"},
     *     {"name"="recipient", "dataType"="string", "required"=false, "description"="Recipient"},
     *     {"name"="name", "dataType"="string", "required"=true, "description"="Transition name"},
     *     {"name"="comment", "dataType"="string", "required"=false, "description"="Comment"}
     *   }
     * )
     */
    public function transitionAction(Request $request)
    {
        $id = $request->get('id');
        $assignedUserId = $request->get('recipient');
        $name = $request->get('name');
        $comment = $request->get('comment');

        if ($comment) {
            $comment = urldecode($comment);
        }

        $taskManager = $this->get('phlexible_task.task_manager');
        $userManager = $this->get('phlexible_user.user_manager');

        $assignUser = null;
        if ($assignedUserId) {
            $assignUser = $userManager->find($assignedUserId);
        }

        $task = $taskManager->find($id);
        $taskManager->updateTask($task, $this->getUser(), $name, $assignUser, $comment);

        return new ResultResponse(true, 'Task transition created.');
    }

    /**
     * Assign task
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/assign", name="tasks_assign")
     * @Method({"GET", "POST"})
     * @ApiDoc(
     *   description="Create status",
     *   requirements={
     *     {"name"="id", "dataType"="string", "required"=true, "description"="Task ID"},
     *     {"name"="recipient", "dataType"="string", "required"=true, "description"="Recipient"},
     *     {"name"="comment", "dataType"="string", "required"=false, "description"="Comment"}
     *   }
     * )
     */
    public function assignAction(Request $request)
    {
        $id = $request->get('id');
        $assignedUserId = $request->get('recipient');
        $comment = $request->get('comment');

        if ($comment) {
            $comment = urldecode($comment);
        }

        $taskManager = $this->get('phlexible_task.task_manager');
        $userManager = $this->get('phlexible_user.user_manager');

        $task = $taskManager->find($id);
        $assignUser = $userManager->find($assignedUserId);

        $taskManager->updateTask($task, $this->getUser(), null, $assignUser, $comment);

        return new ResultResponse(true, 'Task assigned.');
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
     *     {"name"="id", "dataType"="string", "required"=true, "description"="Task ID"}
     *   }
     * )
     */
    public function viewAction(Request $request)
    {
        $id = $request->get('id');

        $taskManager = $this->get('phlexible_task.task_manager');
        $types = $this->get('phlexible_task.types');
        $userManager = $this->get('phlexible_user.user_manager');

        $task = $taskManager->find($id);

        $createUser = $userManager->find($task->getCreateUserId());
        $assignedUser = $userManager->find($task->getAssignedUserId());

        $transitions = array();
        foreach ($task->getTransitions() as $transition) {
            $transitionUser = $userManager->find($transition->getCreateUserId());
            $history[] = array(
                'create_date' => $transition->getCreatedAt()->format('Y-m-d H:i:s'),
                'name'        => $transitionUser->getDisplayName(),
                'status'      => $transition->getNewState(),
                'latest'      => 1,
            );
        }
        $transitions = array_reverse($transitions);

        $comments = array();
        foreach ($task->getComments() as $comment) {
            $commentUser = $userManager->find($comment->getCreateUserId());
            $history[] = array(
                'create_date' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
                'name'        => $commentUser->getDisplayName(),
                'status'      => $comment->getCurrentState(),
                'comment'     => $comment->getComment(),
                'latest'      => 1,
            );
        }

        $type = $types->get($task->getType());

        $data = array(
            'id'             => $task->getId(),
            'type'           => $task->getType(),
            'title'          => $type->getTitle($task),
            'text'           => $type->getText($task),
            'component'      => $type->getComponent(),
            'created'        => $task->getCreateUserId() === $this->getUser()->getId() ? 1 : 0,
            'assigned'       => $task->getAssignedUserId() === $this->getUser()->getId() ? 1 : 0,
            'assigned_user'  => $assignedUser->getDisplayName(),
            'assigned_uid'   => $task->getAssignedUserId(),
            'create_user'    => $createUser->getDisplayName(),
            'create_uid'     => $task->getCreateUserId(),
            'create_date'    => $task->getCreatedAt()->format('Y-m-d H:i:s'),
            'latest_status'  => $task->getFiniteState(),
            'latest_comment' => '',//$latestStatus->getComment(),
            'latest_user'    => '',//$assignedUser->getDisplayName(),
            'latest_uid'     => '',//$latestStatus->getCreateUserId(),
            'latest_date'    => '',//$latestStatus->getCreatedAt()->format('Y-m-d H:i:s'),
            //'recipient_uid'  => $task->getRecipientUserId(),
            //'latest_id'      => $latestStatus->getId(),
            'transitions'    => $transitions,
            'comments'       => $comments,
        );

        return new JsonResponse($data);
    }
}
