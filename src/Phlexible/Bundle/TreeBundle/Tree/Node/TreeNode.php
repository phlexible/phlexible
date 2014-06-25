<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree\Node;

use Phlexible\Bundle\AccessControlBundle\ContentObject\ContentObjectInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeInterface;
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
     * @var integer
     */
    private $id;

    /**
     * @var integer
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
     * @var array
     */
    private $attributes;

    /**
     * @var integer
     */
    private $sort;

    /**
     * @var string
     */
    private $sortMode;

    /**
     * @var string
     */
    private $sortDir;

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
        $this->id = (integer) $id;

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
        $this->parentId = empty($parentId) ? null : (integer) $parentId;

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
    public function getAttribute($key)
    {
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
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * {@inheritdoc}
     */
    public function setSort($sort)
    {
        $this->sort = (integer) $sort;

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
     * Return icon parameters
     *
     * @param string $language
     *
     * @return array
     */
    public function getIconParams($language)
    {
        if ($this->isRoot()) {
            return array();
        }

        $status = '';
        if ($this->isPublished($language)) {
            $status = $this->isAsync($language) ? 'async': 'online';
        }

        $iconParams = array(
            'status'   => $status,
            'instance' => ($this->isInstance() ? ($this->isInstanceMaster() ? 'master' : 'slave') : false),
        );

        if ($this->getSortMode() !== Makeweb_Elements_Tree::SORT_MODE_FREE) {
            $iconParams['sort'] = $this->getSortMode() . '_' . $this->getSortDir();
        }

        return $iconParams;
    }
}
