<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task comment
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="task_comment")
 */
class Comment
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed" = true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $currentStatus;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $comment;

    /**
     * @var string
     * @ORM\Column(name="create_user_id", type="string", length=36, options={"fixed"=true})
     */
    private $createUserId;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var Task
     * @ORM\ManyToOne(targetEntity="Task", inversedBy="comments")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $task;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentStatus()
    {
        return $this->currentStatus;
    }

    /**
     * @param string $currentStatus
     *
     * @return $this
     */
    public function setCurrentStatus($currentStatus)
    {
        $this->currentStatus = $currentStatus;

        return $this;
    }

    /**
     * Return status create date
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Return status create uid
     *
     * @return string
     */
    public function getCreateUserId()
    {
        return $this->createUserId;
    }

    /**
     * @param string $createUserId
     *
     * @return $this
     */
    public function setCreateUserId($createUserId)
    {
        $this->createUserId = $createUserId;

        return $this;
    }

    /**
     * @return Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param Task $task
     *
     * @return $this
     */
    public function setTask($task = null)
    {
        $this->task = $task;
        if (null !== $task) {
            $task->addComment($this);
        }

        return $this;
    }
}
