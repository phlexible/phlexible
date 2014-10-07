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
use Finite\StatefulInterface;

/**
 * Task
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="task")
 */
class Task implements StatefulInterface
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
     * @var string
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="json_array")
     */
    private $payload;

    /**
     * @var string
     * @ORM\Column(name="assigned_user_id", type="string", length=36, options={"fixed" = true})
     */
    private $assignedUserId;

    /**
     * @var array
     * @ORM\Column(name="involved_user_ids", type="simple_array")
     */
    private $involvedUserIds = array();

    /**
     * @var string
     * @ORM\Column(name="finite_state", type="string")
     */
    private $finiteState;

    /**
     * @var Transition[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Transition", mappedBy="task")
     */
    private $transitions;

    /**
     * @var Comment[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="task")
     */
    private $comments;

    public function __construct()
    {
        $this->transitions = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
    public function getAssignedUserId()
    {
        return $this->assignedUserId;
    }

    /**
     * @param string $assignedUserId
     *
     * @return $this
     */
    public function setAssignedUserId($assignedUserId)
    {
        $this->assignedUserId = $assignedUserId;
        $this->addInvolvedUserId($assignedUserId);

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
        $this->addInvolvedUserId($createUserId);

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
     * Return current status
     *
     * @return string
     */
    public function getFiniteState()
    {
        return $this->finiteState;
    }

    /**
     * @param string $finiteState
     *
     * @return $this
     */
    public function setFiniteState($finiteState)
    {
        $this->finiteState = $finiteState;

        return $this;
    }

    /**
     * Return all transitions
     *
     * @return Transition[]
     */
    public function getTransitions()
    {
        return $this->transitions;
    }

    /**
     * @param Transition $transition
     *
     * @return $this
     */
    public function addTransition(Transition $transition)
    {
        if (!$this->transitions->contains($transition)) {
            $this->transitions->add($transition);
            $transition->setTask($this);
        }

        return $this;
    }

    /**
     * @param Transition $transition
     *
     * @return $this
     */
    public function removeTransition(Transition $transition)
    {
        if ($this->transitions->contains($transition)) {
            $this->transitions->removeElement($transition);
            $transition->setTask(null);
        }

        return $this;
    }

    /**
     * Return all comments
     *
     * @return Comment[]
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     *
     * @return $this
     */
    public function addComment(Comment $comment)
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setTask($this);
        }

        return $this;
    }

    /**
     * @param Comment $comment
     *
     * @return $this
     */
    public function removeComment(Comment $comment)
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            $comment->setTask(null);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return array
     */
    public function getInvolvedUserIds()
    {
        return $this->involvedUserIds;
    }

    /**
     * @param array $involvedUserIds
     *
     * @return $this
     */
    public function setInvolvedUserIds($involvedUserIds)
    {
        $this->involvedUserIds = $involvedUserIds;

        return $this;
    }

    /**
     * @param string $involvedUserId
     *
     * @return $this
     */
    public function addInvolvedUserId($involvedUserId)
    {
        if (!in_array($involvedUserId, $this->involvedUserIds)) {
            $this->involvedUserIds[] = $involvedUserId;
        }

        return $this;
    }

    /**
     * @param string $involvedUserId
     *
     * @return $this
     */
    public function removeInvolvedUserId($involvedUserId)
    {
        if (in_array($involvedUserId, $this->involvedUserIds)) {
            unset($this->involvedUserIds[array_search($involvedUserId, $this->involvedUserIds)]);
        }

        return $this;
    }
}
