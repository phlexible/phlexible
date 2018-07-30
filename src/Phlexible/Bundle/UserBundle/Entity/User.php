<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    const PROPERTY_THEME = 'theme';
    const PROPERTY_INTERFACE_LANGUAGE = 'interfaceLanguage';
    const PROPERTY_NO_PASSWORD_CHANGE = 'noPasswordChange';
    const PROPERTY_FORCE_PASSWORD_CHANGE = 'forcePasswordChange';

    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed" = true})
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    private $properties = [];

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
     * @var Group[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     * @ORM\JoinTable(name="user_group")
     */
    protected $groups;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->modifiedAt = new \DateTime();

        parent::__construct();
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
    public function getDisplayName()
    {
        if ($this->firstname && $this->lastname) {
            return $this->firstname.' '.$this->lastname;
        }

        return $this->username;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     *
     * @return $this
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     *
     * @return $this
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

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
     * @return Group[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasGroup($name)
    {
        if ($name instanceof GroupInterface) {
            return $this->groups->contains($name);
        }

        return $this->groups->containsKey($name);
    }

    /**
     * @param GroupInterface $group
     *
     * @return $this
     */
    public function addGroup(GroupInterface $group)
    {
        if (!$this->hasGroup($group)) {
            $this->groups->set($group->getName(), $group);
            $group->addUser($this);
        }

        return $this;
    }

    /**
     * @param GroupInterface $group
     *
     * @return $this
     */
    public function removeGroup(GroupInterface $group)
    {
        if ($this->hasGroup($group)) {
            $this->groups->removeElement($group);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Set properties.
     *
     * @param array $properties
     *
     * @return $this
     */
    public function setProperties($properties)
    {
        $this->properties = [];

        return $this->addProperties($properties);
    }

    /**
     * Add properties.
     *
     * @param array $properties
     *
     * @return $this
     */
    public function addProperties($properties)
    {
        foreach ($properties as $key => $value) {
            $this->setProperty($key, $value);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param string $defaultValue
     *
     * @return string
     */
    public function getProperty($key, $defaultValue = null)
    {
        if (isset($this->properties[$key])) {
            return $this->properties[$key];
        }

        return $defaultValue;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setProperty($key, $value)
    {
        $this->properties[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function removeProperty($key)
    {
        if (isset($this->properties[$key])) {
            unset($this->properties[$key]);
        }

        return $this;
    }

    /**
     * @param string $defaultLanguage
     *
     * @return string
     */
    public function getInterfaceLanguage($defaultLanguage = null)
    {
        return $this->getProperty('interfaceLanguage', $defaultLanguage);
    }

    /**
     * @param string $interfaceLanguage
     *
     * @return $this
     */
    public function setInterfaceLanguage($interfaceLanguage)
    {
        return $this->setProperty('interfaceLanguage', $interfaceLanguage);
    }

    /**
     * @param string $defaultLanguage
     *
     * @return string
     */
    public function getContentLanguage($defaultLanguage = null)
    {
        return $this->getProperty('contentLanguage', $defaultLanguage);
    }

    /**
     * @param string $contentLanguage
     *
     * @return $this
     */
    public function setContentLanguage($contentLanguage)
    {
        return $this->setProperty('contentLanguage', $contentLanguage);
    }
}
