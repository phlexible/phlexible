<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Access control entry
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="access_control", uniqueConstraints={@ORM\UniqueConstraint(columns={"content_type", "content_id", "object_type", "object_id", "right_name", "content_language"})})
 */
class AccessControlEntry
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="content_type", type="string", length=100)
     */
    private $contentType;

    /**
     * @var string
     * @ORM\Column(name="content_id", type="string", length=100)
     */
    private $contentId;

    /**
     * @var string
     * @ORM\Column(name="object_type", type="string", length=100)
     */
    private $objectType;

    /**
     * @var string
     * @ORM\Column(name="object_id", type="string", length=100)
     */
    private $objectId;

    /**
     * @var string
     * @ORM\Column(name="permission", type="string", length=100)
     */
    private $permission;

    /**
     * @var string
     * @ORM\Column(name="content_language", type="string", length=2, nullable=true, options={"fixed"=true})
     */
    private $contentLanguage;

    /**
     * @var string
     * @ORM\Column(name="right_type", type="string", length=100)
     */
    private $rightType;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $inherit;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
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
    public function getRightType()
    {
        return $this->rightType;
    }

    /**
     * @param string $rightType
     *
     * @return $this
     */
    public function setRightType($rightType)
    {
        $this->rightType = $rightType;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * @param string $contentId
     *
     * @return $this
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentLanguage()
    {
        return $this->contentLanguage;
    }

    /**
     * @param string $contentLanguage
     *
     * @return $this
     */
    public function setContentLanguage($contentLanguage)
    {
        $this->contentLanguage = $contentLanguage;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * @param string $objectType
     *
     * @return $this
     */
    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @param string $objectId
     *
     * @return $this
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * @return string
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param string $rightName
     *
     * @return $this
     */
    public function setPermission($rightName)
    {
        $this->permission = $rightName;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getInherit()
    {
        return $this->inherit;
    }

    /**
     * @param boolean $inherit
     *
     * @return $this
     */
    public function setInherit($inherit)
    {
        $this->inherit = $inherit;

        return $this;
    }
}
