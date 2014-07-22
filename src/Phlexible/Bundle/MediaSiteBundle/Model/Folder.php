<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Model;

use Phlexible\Bundle\AccessControlBundle\ContentObject\ContentObjectInterface;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderIdentifier;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderIterator;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;
use Phlexible\Component\Identifier\IdentifiableInterface;

/**
 * Folder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Folder implements FolderInterface, \IteratorAggregate, IdentifiableInterface, ContentObjectInterface
{
    /**
     * @var SiteInterface
     */
    private $site;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $parentId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $physicalPath;

    /**
     * @var AttributeBag
     */
    private $attributes;

    /**
     * @var string
     */
    private $createUserId;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var string
     */
    private $modifyUserId;

    /**
     * @var \DateTime
     */
    private $modifiedAt;

    public function __construct()
    {
        $this->attributes = new AttributeBag();
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
    public function setAttributes(AttributeBag $attributes)
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
