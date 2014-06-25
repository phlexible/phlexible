<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="task")
 */
class Task
{
    const STATUS_OPEN     = 'open';
    const STATUS_REJECTED = 'rejected';
    const STATUS_FINISHED = 'finished';
    const STATUS_REOPENED = 'reopened';
    const STATUS_CLOSED   = 'closed';

    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed" = true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="create_user_id", type="string", length=36, options={"fixed" = true})
     */
    private $createUserId;

    /**
     * @var string
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="recipient_user_id", type="string", length=36, options={"fixed" = true})
     */
    private $recipientUserId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(name="current_status", type="string")
     */
    private $currentStatus;

    /**
     * @var \DateTime
     * @ORM\Column(name="closed_at", type="datetime", nullable=true)
     */
    private $closedAt;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $payload;

    /**
     * @var Status[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Status", mappedBy="task")
     */
    private $status;

    public function __construct()
    {
        $this->status = new ArrayCollection();
    }

    /**
     * Return task ID
     *
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getRecipientUserId()
    {
        return $this->recipientUserId;
    }

    /**
     * @param string $recipientUserId
     *
     * @return $this
     */
    public function setRecipientUserId($recipientUserId)
    {
        $this->recipientUserId = $recipientUserId;

        return $this;
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param array $payload
     *
     * @return $this
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
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
     * @return \DateTime
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }

    /**
     * @param \DateTime $closedAt
     *
     * @return $this
     */
    public function setClosedAt(\DateTime $closedAt)
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * Return latest status item
     *
     * @return Status
     */
    public function getLatestStatus()
    {
        if (!$this->status->count()) {
            return null;
        }

        return $this->status->last();
    }

    /**
     * Return current status
     *
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
     * Return all status items
     *
     * @return Status[]
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param Status $status
     *
     * @return $this
     */
    public function addStatus(Status $status)
    {
        if (!$this->status->contains($status)) {
            $this->status->add($status);
            $status->setTask($this);
        }

        return $this;
    }

    /**
     * @param Status $status
     *
     * @return $this
     */
    public function removeStatus(Status $status)
    {
        if ($this->status->contains($status)) {
            $this->status->removeElement($status);
            $status->setTask(null);
        }

        return $this;
    }
}
