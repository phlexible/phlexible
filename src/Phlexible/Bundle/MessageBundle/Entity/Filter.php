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
use Phlexible\Bundle\MessageBundle\Criteria\Criteria;

/**
 * Filter.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity(repositoryClass = "Phlexible\Bundle\MessageBundle\Entity\Repository\FilterRepository")
 * @ORM\Table(name="message_filter")
 */
class Filter
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
     * @ORM\Column(name="user_id", type="string", length=36, options={"fixed" = true})
     */
    private $userId;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    private $private = 0;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="modified_at", type="datetime")
     */
    private $modifiedAt;

    /**
     * @var Criteria
     * @ORM\Column(type="object", nullable=true)
     */
    private $criteria;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->criteria = new Criteria();
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
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     *
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPrivate()
    {
        return $this->private;
    }

    /**
     * @param bool $private
     *
     * @return $this
     */
    public function setPrivate($private = true)
    {
        $this->private = (bool) $private;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     * @param \DateTime $createTime
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createTime)
    {
        $this->createdAt = $createTime;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param \DateTime $modifyTime
     *
     * @return $this
     */
    public function setModifiedAt(\DateTime $modifyTime)
    {
        $this->modifiedAt = $modifyTime;

        return $this;
    }

    /**
     * @return Criteria
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param Criteria $criteria
     *
     * @return $this
     */
    public function setCriteria(Criteria $criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }
}
