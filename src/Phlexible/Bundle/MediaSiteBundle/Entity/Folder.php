<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderIdentifier;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderIterator;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;

/**
 * Folder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="media_site_folder")
 */
class Folder
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="site_id", type="string", length=36, options={"fixed"=true})
     */
    private $siteId;

    /**
     * @var string
     * @ORM\Column(name="parent_id", type="string", length=36, nullable=true, options={"fixed"=true})
     */
    private $parentId;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @var array
     * @ORM\Column(type="object", nullable=true)
     */
    private $attributes;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $deleted = false;

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
     * @var File[]
     * @ORM\OneToMany(targetEntity="File", mappedBy="folder")
     */
    private $files;

    /**
     * @var SiteInterface
     */
    private $site;

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
     * @return FolderIdentifier
     */
    public function getIdentifier()
    {
        return new FolderIdentifier($this->getId());
    }

    /**
     * @return array
     */
    public function getContentObjectIdentifiers()
    {
        return array(
            'type' => 'folder',
            'id'   => $this->id,
        );
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
        $path = array($this->id);
        $current = $this;
        while ($current->getParentId()) {
            $path[] = $current->getParentId();
            $current = $this->site->findFolder($current->getParentId());
        }

        return array_reverse($path);
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