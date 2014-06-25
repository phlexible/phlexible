<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity(repositoryClass = "Phlexible\Bundle\MessageBundle\Entity\Repository\MessageRepository")
 * @ORM\Table(name="message", indexes={@ORM\Index(columns={"created_at"})})
 */
class Message
{
    const PRIORITY_LOW    = 0;
    const PRIORITY_NORMAL = 1;
    const PRIORITY_HIGH   = 2;
    const PRIORITY_URGENT = 3;

    const TYPE_INFO = 0;
    const TYPE_ERROR = 1;
    const TYPE_AUDIT = 2;

    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed" = true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $subject;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $body;

    /**
     * @var int
     * @ORM\Column(type="smallint", length=1)
     */
    private $priority = self::PRIORITY_NORMAL;

    /**
     * @var int
     * @ORM\Column(type="smallint", length=1)
     */
    private $type = self::TYPE_INFO;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $channel;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resource;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $user;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return array();
    }

    /**
     * @param string $subject
     * @param string $body
     * @param int    $priority
     * @param int    $type
     * @param string $channel
     * @param string $resource
     * @param string $user
     *
     * @return Message
     */
    public static function create($subject = null, $body = null, $priority = null, $type = null, $channel = null, $resource = null, $user = null)
    {
        $staticMessage = new static();
        $defaults = $staticMessage->getDefaults();

        $message = new self();
        if ($subject === null && isset($defaults['subject'])) {
            $subject = $defaults['subject'];
        }
        $message->setSubject($subject);

        if ($body === null && isset($defaults['body'])) {
            $body = $defaults['body'];
        }
        $message->setBody($body);

        if ($priority === null && isset($defaults['priority'])) {
            $priority = $defaults['priority'];
        }
        $message->setPriority($priority);

        if ($type === null && isset($defaults['type'])) {
            $type = $defaults['type'];
        }
        $message->setType($type);

        if ($channel === null && isset($defaults['channel'])) {
            $channel = $defaults['channel'];
        }
        $message->setChannel($channel);

        if ($resource === null && isset($defaults['resource'])) {
            $resource = $defaults['resource'];
        }
        $message->setResource($resource);

        if ($user === null && isset($defaults['user'])) {
            $user = $defaults['user'];
        }
        $message->setResource($user);

        return $message;
    }

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
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     *
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = (int) $priority;

        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = (int) $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param string $channel
     *
     * @return $this
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param string $resource
     *
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

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
}
