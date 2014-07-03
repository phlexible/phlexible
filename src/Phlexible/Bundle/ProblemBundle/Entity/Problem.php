<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ProblemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Problem
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="problem")
 */
class Problem
{
    const SEVERITY_CRITICAL = 'critical';
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_NOTICE = 'notice';
    const SEVERITY_INFO = 'info';

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="check_class", type="string", length=255)
     */
    private $checkClass;

    /**
     * @var string
     * @ORM\Column(name="icon_class", type="string", length=255, nullable=true)
     */
    private $iconClass;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $severity;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $msg;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hint;

    /**
     * @var array
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_checked_at", type="datetime")
     */
    private $lastCheckedAt;

    /**
     * @var bool
     */
    private $isLive = false;

    /**
     * @param string $severity
     * @param string $msg
     * @param string $hint
     * @param array  $link
     */
    public function __construct($severity = null, $msg = null, $hint = null, array $link = null)
    {
        if ($severity !== null) {
            $this->severity = $severity;
        }

        if ($msg !== null) {
            $this->msg = $msg;
        }

        if ($hint !== null) {
            $this->hint = $hint;
        }

        if ($link !== null) {
            $this->link = $link;
        }
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $checkClass
     *
     * @return $this
     */
    public function setCheckClass($checkClass)
    {
        $this->checkClass = $checkClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getCheckClass()
    {
        return $this->checkClass;
    }

    /**
     * @return string
     */
    public function getIconClass()
    {
        return $this->iconClass;
    }

    /**
     * @param string $iconClass
     *
     * @return $this
     */
    public function setIconClass($iconClass)
    {
        $this->iconClass = $iconClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @param string $severity
     *
     * @return $this
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->msg;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->msg = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * @param string $hint
     *
     * @return $this
     */
    public function setHint($hint)
    {
        $this->hint = $hint;

        return $this;
    }

    /**
     * @return array
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param array $link
     *
     * @return $this
     */
    public function setLink(array $link)
    {
        $this->link = $link;

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
    public function getLastCheckedAt()
    {
        return $this->lastCheckedAt;
    }

    /**
     * @param \DateTime $lastCheckedAt
     *
     * @return $this
     */
    public function setLastCheckedAt(\DateTime $lastCheckedAt)
    {
        $this->lastCheckedAt = $lastCheckedAt;

        return $this;
    }

    /**
     * @param bool $isLive
     *
     * @return $this
     */
    public function setLive($isLive = true)
    {
        $this->isLive = $isLive;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLive()
    {
        return $this->isLive;
    }

    /**
     * Return array represantation of this problem
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'severity' => $this->severity,
            'msg'      => $this->msg,
            'hint'     => $this->hint,
            'link'     => !empty($this->link) ? $this->link : null,
            'iconCls'  => $this->iconClass,
        );
    }
}
