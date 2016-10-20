<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
    const PRIORITY_LOW = 0;
    const PRIORITY_NORMAL = 1;
    const PRIORITY_HIGH = 2;
    const PRIORITY_URGENT = 3;

    const TYPE_INFO = 0;
    const TYPE_ERROR = 1;

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
    private $role;

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

    /**
     * @param string    $subject
     * @param string    $body
     * @param int       $priority
     * @param int       $type
     * @param string    $channel
     * @param string    $role
     * @param string    $user
     * @param \DateTime $createdAt
     */
    public function __construct($subject, $body, $priority, $type, $channel, $role, $user, \DateTime $createdAt)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->priority = $priority;
        $this->type = $type;
        $this->channel = $channel;
        $this->role = $role;
        $this->user = $user;
        $this->createdAt = $createdAt;
    }

    /**
     * @return string|null
     */
    public static function getDefaultSubject()
    {
        return null;
    }

    /**
     * @return string|null
     */
    public static function getDefaultBody()
    {
        return null;
    }

    /**
     * @return int|null
     */
    public static function getDefaultPriority()
    {
        return self::PRIORITY_NORMAL;
    }

    /**
     * @return int|null
     */
    public static function getDefaultType()
    {
        return self::TYPE_INFO;
    }

    /**
     * @return string|null
     */
    public static function getDefaultChannel()
    {
        return null;
    }

    /**
     * @return string|null
     */
    public static function getDefaultRole()
    {
        return null;
    }

    /**
     * @return string|null
     */
    public static function getDefaultUser()
    {
        return null;
    }

    /**
     * @return \DateTime
     */
    public static function getDefaultCreatedAt()
    {
        return new \DateTime;
    }

    /**
     * @param string    $subject
     * @param string    $body
     * @param int       $priority
     * @param int       $type
     * @param string    $channel
     * @param string    $role
     * @param string    $user
     * @param \DateTime $createdAt
     *
     * @return Message
     */
    public static function create(
        $subject = null,
        $body = null,
        $priority = null,
        $type = null,
        $channel = null,
        $role = null,
        $user = null,
        \DateTime $createdAt = null)
    {
        if ($subject === null) {
            $subject = static::getDefaultSubject();
        }

        if ($body === null) {
            $body = static::getDefaultBody();
        }

        if ($priority === null) {
            $priority = static::getDefaultPriority();
        }

        if ($type === null) {
            $type = static::getDefaultType();
        }

        if ($channel === null) {
            $channel = static::getDefaultChannel();
        }

        if ($role === null) {
            $role = static::getDefaultRole();
        }

        if ($user === null) {
            $user = static::getDefaultUser();
        }

        if ($createdAt === null) {
            $createdAt = static::getDefaultCreatedAt();
        }

        /* @var $message Message */
        $message = new self($subject, $body, $priority, $type, $channel, $role, $user, $createdAt);

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
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
