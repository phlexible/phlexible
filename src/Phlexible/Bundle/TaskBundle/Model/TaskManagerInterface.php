<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\Model;

use Phlexible\Bundle\TaskBundle\Entity\Status;
use Phlexible\Bundle\TaskBundle\Entity\Task;
use Phlexible\Bundle\TaskBundle\Task\Type\TypeInterface;

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
     * Create task
     *
     * @param TypeInterface $type
     * @param string        $createUserId
     * @param string        $recipientUserId
     * @param array         $payload
     * @param string        $comment
     *
     * @return Task
     */
    public function createTask(TypeInterface $type, $createUserId, $recipientUserId, array $payload, $comment);

    /**
     * Create task status
     *
     * @param Task   $task
     * @param string $userId
     * @param string $comment
     * @param string $newStatus
     *
     * @return Status
     */
    public function createStatus(Task $task, $userId, $comment, $newStatus = Task::STATUS_OPEN);
}
