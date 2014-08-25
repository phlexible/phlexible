<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Model;

use Phlexible\Bundle\AccessControlBundle\ContentObject\ContentObjectInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;

/**
 * Tree node
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeNode implements TreeNodeInterface, \IteratorAggregate, ContentObjectInterface
{
    /**
     * @var TreeInterface
     */
    private $tree;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $parentId;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $typeId;

    /**
     * @var int
     */
    private $sort = 0;

    /**
     * @var string
     */
    private $sortMode = 'free';

    /**
     * @var string
     */
    private $sortDir = 'asc';

    /**
     * @var bool
     */
    private $inNavigation = false;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var string
     */
    private $createUid;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * Get an iterator for this node.
     *
     * @return TreeIterator
     */
    public function getIterator()
    {
        return new TreeIterator($this);
    }

    /**
     * @return array
     */
    public function getContentObjectIdentifiers()
    {
        return array(
           'type' => 'treenode',
           'id'   => $this->getId()
        );
    }

    /**
     * @return array
     */
    public function getContentObjectPath()
    {
        return $this->getTree()->getPath($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * {@inheritdoc}
     */
    public function setTree(TreeInterface $tree)
    {
        $this->tree = $tree;

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
        $this->id = (int) $id;

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
        $this->parentId = empty($parentId) ? null : (int) $parentId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot()
    {
        return $this->parentId === null;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * {@inheritdoc}
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;

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
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * {@inheritdoc}
     */
    public function setSort($sort)
    {
        $this->sort = (int) $sort;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortMode()
    {
        return $this->sortMode;
    }

    /**
     * {@inheritdoc}
     */
    public function setSortMode($sortMode)
    {
        $this->sortMode = $sortMode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortDir()
    {
        return $this->sortDir;
    }

    /**
     * {@inheritdoc}
     */
    public function setSortDir($sortDir)
    {
        $this->sortDir = $sortDir;

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
        return $this->createUid;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreateUserId($createUid)
    {
        $this->createUid = $createUid;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInNavigation()
    {
        return $this->inNavigation;
    }

    /**
     * {@inheritdoc}
     */
    public function setInNavigation($inNavigation)
    {
        $this->inNavigation = $inNavigation;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCache()
    {
        return $this->getAttribute('cache', array());
    }

    /**
     * {@inheritdoc}
     */
    public function setCache($cache)
    {
        return $this->setAttribute('cache', $cache);
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->getAttribute('controller');
    }

    /**
     * @param string $controller
     *
     * @return $this
     */
    public function setController($controller)
    {
        return $this->setAttribute('controller', $controller);
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->getAttribute('routes', array());
    }

    /**
     * @param array $routes
     *
     * @return $this
     */
    public function setRoutes(array $routes)
    {
        return $this->setAttribute('routes', $routes);
    }

    /**
     * @return boolean
     */
    public function getNeedsAuthentication()
    {
        return $this->getAttribute('needs_authentication', false);
    }

    /**
     * @param boolean $needsAuthentication
     *
     * @return $this
     */
    public function setNeedsAuthentication($needsAuthentication)
    {
        return $this->setAttribute('needs_authentication', !!$needsAuthentication);
    }
}
