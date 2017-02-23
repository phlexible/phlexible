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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Phlexible\Component\Volume\VolumeInterface;

/**
 * Folder.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\MappedSuperclass
 */
class Folder implements FolderInterface
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="volume_id", type="string", length=36, options={"fixed"=true})
     */
    protected $volumeId;

    /**
     * @var string
     * @ORM\Column(name="parent_id", type="string", length=36, nullable=true, options={"fixed"=true})
     */
    protected $parentId;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $path;

    /**
     * @var array
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $attributes = [];

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $deleted = false;

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
     * @var File[]
     * @ORM\OneToMany(targetEntity="File", mappedBy="folder")
     */
    protected $files;

    /**
     * @var VolumeInterface
     */
    protected $volume;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    /**
     * @return FolderIterator|\Traversable
     */
    public function getIterator()
    {
        return new FolderIterator($this);
    }

    /**
     * @return array
     */
    public function getContentObjectIdentifiers()
    {
        return [
            'type' => 'folder',
            'id' => $this->id,
        ];
    }

    /**
     * @return array
     */
    public function getContentObjectPath()
    {
        return $this->getIdPath();
    }

    /**
     * @return array
     */
    public function getIdPath()
    {
        $path = [$this->id];
        $current = $this;
        while ($current->getParentId()) {
            $path[] = $current->getParentId();
            $current = $this->volume->findFolder($current->getParentId());
        }

        return array_reverse($path);
    }

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
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * {@inheritdoc}
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

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
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhysicalPath()
    {
        return null;
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
        return $this->attributes->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($key, $value)
    {
        $this->attributes->set($key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttribute($key)
    {
        $this->attributes->remove($key);

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
    public function setCreatedAt(\DateTime $createTime)
    {
        $this->createdAt = $createTime;

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
    public function setModifiedAt(\DateTime $modifyTime)
    {
        $this->modifiedAt = $modifyTime;

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

    /**
     * {@inheritdoc}
     */
    public function isRoot()
    {
        return $this->parentId === null;
    }
}
