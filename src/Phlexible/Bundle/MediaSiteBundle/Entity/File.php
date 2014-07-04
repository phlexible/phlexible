<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Bundle\MediaSiteBundle\File\FileIdentifier;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;

/**
 * File
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="media_site_file")
 */
class File implements FileInterface
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    private $id;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"default"=1})
     */
    private $version = 1;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="mime_type", type="string", length=100)
     */
    private $mimeType;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, options={"fixed"=true})
     */
    private $hash;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    private $attributes;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $deleted = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $hidden = false;

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
     * @var string
     * @ORM\Column(name="modify_user_id", type="string", length=36, options={"fixed"=true})
     */
    private $modifyUserId;

    /**
     * @var \DateTime
     * @ORM\Column(name="modified_at", type="datetime")
     */
    private $modifiedAt;

    /**
     * @var Folder
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="files")
     * @ORM\JoinColumn(name="folder_id", referencedColumnName="id")
     */
    private $folder;

    /**
     * @var SiteInterface
     */
    private $site;

    /**
     * @return FileIdentifier
     */
    public function getIdentifier()
    {
        return new FileIdentifier($this->getID());
    }

    /**
     * {@inheritdoc}
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * {@inheritdoc}
     */
    public function setSite(SiteInterface $site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion($version)
    {
        $this->version = (int) $version;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFolderId()
    {
        return $this->folder->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function setFolderId($folderId)
    {
        $this->folderId = $folderId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * {@inheritdoc}
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhysicalPath()
    {
        return $this->physicalPath;
    }

    /**
     * {@inheritdoc}
     */
    public function setPhysicalPath($physicalPath)
    {
        $this->physicalPath = $physicalPath;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function setSize($size)
    {
        $this->size = (int) $size;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * {@inheritdoc}
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * {@inheritdoc}
     */
    public function setHidden($hidden = true)
    {
        $this->hidden = (bool) $hidden;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($key, $default = null)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateUserId()
    {
        return $this->createUserId;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreateUserId($createUserId)
    {
        $this->createUserId = $createUserId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setModifiedAt(\DateTime $modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModifyUserId()
    {
        return $this->modifyUserId;
    }

    /**
     * {@inheritdoc}
     */
    public function setModifyUserId($modifyUserId)
    {
        $this->modifyUserId = $modifyUserId;

        return $this;
    }
}