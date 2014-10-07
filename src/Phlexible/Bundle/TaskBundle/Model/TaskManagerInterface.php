<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\Model;

use Finite\Transition\TransitionInterface;
use Phlexible\Bundle\TaskBundle\Entity\Comment;
use Phlexible\Bundle\TaskBundle\Entity\Status;
use Phlexible\Bundle\TaskBundle\Entity\Task;
use Phlexible\Bundle\TaskBundle\Entity\Transition;
use Phlexible\Bundle\TaskBundle\Task\Type\TypeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Task manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TaskManagerInterface
{
    /**
     * @param string $id
     *
     * @return Task
     */
    public function find($id);

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return Task[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param string $userId
     * @param array  $status
     * @param array  $sort
     * @param int    $limit
     * @param int    $start
     *
     * @return Task[]
     */
    public function findByCreatedByAndStatus($userId, array $status = array(), array $sort = array(), $limit = null, $start = null);

    /**
     * @param string $userId
     * @param array  $status
     *
     * @return int
     */
    public function countByCreatedByAndStatus($userId, array $status = array());

    /**
     * @param string $userId
     * @param array  $status
     * @param array  $sort
     * @param int    $limit
     * @param int    $start
     *
     * @return Task[]
     */
    public function findByAssignedToAndStatus($userId, array $status = array(), array $sort = array(), $limit = null, $start = null);

    /**
     * @param string $userId
     * @param array  $status
     *
     * @return int
     */
    public function countByAssignedToAndStatus($userId, array $status = array());

    /**
     * @param string $userId
     * @param array  $status
     * @param array  $sort
     * @param int    $limit
     * @param int    $start
     *
     * @return Task[]
     */
    public function findByInvolvementAndStatus($userId, array $status = array(), array $sort = array(), $limit = null, $start = null);

    /**
     * @param string $userId
     * @param array  $status
     *
     * @return int
     */
    public function countByInvolvementAndStatus($userId, array $status = array());

    /**
     * @param array $status
     * @param array $sort
     * @param int   $limit
     * @param int   $start
     *
     * @return Task[]
     */
    public function findByStatus(array $status = array(), array $sort = array(), $limit = null, $start = null);

    /**
     * @param array $status
     *
     * @return int
     */
    public function countByStatus(array $status = array());

    /**
     * @param array $payload
     *
     * @return Task
     */
    public function findOneByPayload(array $payload);

    /**
     * @param Task $task
     *
     * @return TransitionInterface[]
     */
    public function getTransitions(Task $task);

    /**
     * Create task
     *
     * @param TypeInterface $type
     * @param UserInterface $createUser
     * @param UserInterface $assignedUser
     * @param array         $payload
     * @param string        $description
     *
     * @return Task
     */
    public function createTask(TypeInterface $type, UserInterface $createUser, UserInterface $assignedUser, array $payload, $description);

    /**
     * @param Task               $task
     * @param UserInterface      $byUser
     * @param string|null        $status
     * @param UserInterface|null $assignUser
     * @param string|null        $comment
     *
     * @return Comment
     */
    public function updateTask(Task $task, UserInterface $byUser, $status = null, UserInterface $assignUser = null, $comment = null);
}
