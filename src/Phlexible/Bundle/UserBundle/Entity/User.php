<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * User
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User implements AdvancedUserInterface
{
    const PROPERTY_THEME = 'theme';
    const PROPERTY_INTERFACE_LANGUAGE = 'interfaceLanguage';
    const PROPERTY_NO_PASSWORD_CHANGE    = 'noPasswordChange';
    const PROPERTY_NO_PASSWORD_EXPIRE    = 'noPasswordExpire';
    const PROPERTY_FORCE_PASSWORD_CHANGE = 'forcePasswordChange';

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
    private $username;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @var string
     */
    private $plainPassword;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $salt;

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
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @var \DateTime
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    private $expiresAt;

    /**
     * @var string
     * @ORM\Column(name="password_token", type="string", length=36, nullable=true, options={"fixed"=true})
     */
    private $passwordToken;

    /**
     * @var \DateTime
     * @ORM\Column(name="password_changed_at", type="datetime", nullable=true)
     */
    private $passwordChangedAt;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default"=true})
     */
    private $enabled = true;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    private $properties = array();

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
    private $groups;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    private $roles = array();

    public function __construct()
    {
        $this->groups = new ArrayCollection();
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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     *
     * @return $this
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     *
     * @return $this
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        if ($this->firstname && $this->lastname) {
            return $this->firstname . ' ' . $this->lastname;
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
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime $expireDate
     *
     * @return $this
     */
    public function setExpiresAt(\DateTime $expireDate = null)
    {
        $this->expiresAt = $expireDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getPasswordToken()
    {
        return $this->passwordToken;
    }

    /**
     * @param string $passwordToken
     *
     * @return $this
     */
    public function setPasswordToken($passwordToken)
    {
        $this->passwordToken = $passwordToken;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPasswordChangedAt()
    {
        return $this->passwordChangedAt;
    }

    /**
     * @param \DateTime $passwordChangeTime
     *
     * @return $this
     */
    public function setPasswordChangedAt(\DateTime $passwordChangeTime = null)
    {
        $this->passwordChangedAt = $passwordChangeTime;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

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
     * @param Group $group
     *
     * @return bool
     */
    public function hasGroup($group)
    {
        return $this->groups->contains($group);
    }

    /**
     * @param Group $group
     *
     * @return $this
     */
    public function addGroup(Group $group)
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->addUser($this);
        }

        return $this;
    }

    /**
     * @param Group $group
     *
     * @return $this
     */
    public function removeGroup($group)
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->addUser(null);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return array_search($role, $this->roles) !== false;
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function addRole($role)
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Remove a role
     *
     * @param string $role
     *
     * @return $this
     */
    public function removeRole($role)
    {
        if ($this->hasRole($role)) {
            unset($this->roles[array_search($role, $this->roles)]);
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
     * Set properties
     *
     * @param array $properties
     *
     * @return $this
     */
    public function setProperties($properties)
    {
        $this->properties = array();

        return $this->addProperties($properties);
    }

    /**
     * Add properties
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

    /**
     * @return float
     */
    public function getPasswordChangeDays()
    {
        $result = round((time() - strtotime($this->passwordChangedAt)) / 60 / 60 / 24);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return null === $this->getExpiresAt() || $this->getExpiresAt() > new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return !$this->getProperty(self::PROPERTY_FORCE_PASSWORD_CHANGE, false);
    }
}

