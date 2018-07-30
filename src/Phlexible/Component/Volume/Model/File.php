<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume\Model;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Component\Volume\VolumeInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * File.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\MappedSuperclass
 */
class File implements FileInterface
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default"=1})
     */
    protected $version = 1;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="mime_type", type="string", length=100)
     */
    protected $mimeType;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, options={"fixed"=true})
     */
    protected $hash;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $size;

    /**
     * @var array
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $attributes = array();

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $deleted = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $hidden = false;

    /**
     * @var string
     * @ORM\Column(name="create_user_id", type="string", length=36, options={"fixed"=true})
     */
    protected $createUserId;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var string
     * @ORM\Column(name="modify_user_id", type="string", length=36, options={"fixed"=true})
     */
    protected $modifyUserId;

    /**
     * @var \DateTime
     * @ORM\Column(name="modified_at", type="datetime")
     */
    protected $modifiedAt;

    /**
     * @var Folder
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="files")
     * @ORM\JoinColumn(name="folder_id", referencedColumnName="id")
     */
    protected $folder;

    /**
     * @var VolumeInterface
     */
    protected $volume;

    /**
     * {@inheritdoc}
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * {@inheritdoc}
     */
    public function setVolume(VolumeInterface $volume)
    {
        $this->volume = $volume;
        $this->volumeId = $volume->getId();

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
     * @return Folder
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @param FolderInterface $folder
     *
     * @return $this
     */
    public function setFolder(FolderInterface $folder)
    {
        $this->folder = $folder;

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
        if (!$this->hash) {
            throw new FileNotFoundException($this->getName() . ' (no hash available)');
        }

        $rootDir = rtrim($this->getVolume()->getRootDir(), '/');
        $physicalPath = $rootDir.'/'.$this->hash;

        return $physicalPath;
    }

    /**
     * {@inheritdoc}
     */
    public function setPhysicalPath($physicalPath)
    {
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
        if (!isset($this->attributes[$key])) {
            return $default;
        }

        return $this->attributes[$key];
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
    public function removeAttribute($key)
    {
        if (isset($this->attributes[$key])) {
            unset($this->attributes[$key]);
        }

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
