<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\TreeBundle\Mediator\MediatorInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;

/**
 * Delegating content tree
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingContentTree implements ContentTreeInterface, \IteratorAggregate
{
    /**
     * @var TreeInterface
     */
    private $tree;

    /**
     * @var Siteroot
     */
    private $siteroot;

    /**
     * @var MediatorInterface
     */
    private $mediator;

    /**
     * @var string
     */
    private $language;

    /**
     * @param TreeInterface     $tree
     * @param Siteroot          $siteroot
     * @param MediatorInterface $mediator
     * @param string            $language
     */
    public function __construct(TreeInterface $tree, Siteroot $siteroot, MediatorInterface $mediator, $language = null)
    {
        $this->tree = $tree;
        $this->siteroot = $siteroot;
        $this->mediator = $mediator;
        $this->language = $language;
    }

    /**
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return TreeIterator
     */
    public function getIterator()
    {
        return new TreeIterator($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getSiterootId()
    {
        return $this->siteroot->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getSiteroot()
    {
        return $this->siteroot;
    }

    /**
     * {@inheritdoc}
     */
    public function isDefaultSiteroot()
    {
        return $this->getSiteroot()->isDefault();
    }

    /**
     * {@inheritdoc}
     */
    public function getUrls()
    {
        return $this->getSiteroot()->getUrls();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultUrl()
    {
        return $this->getSiteroot()->getDefaultUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function getNavigations()
    {
        return $this->getSiteroot()->getUrls();
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecialTids($language = null)
    {
        return $this->getSiteroot()->getSpecialTidsForLanguage($language ?: $this->language);
    }

    /**
     * {@inheritdoc}
     */
    public function createContentTreeNodeFromTreeNode(TreeNodeInterface $treeNode)
    {
        $contentNode = new ContentTreeNode();
        $contentNode
            ->setLanguage($this->language)
            ->setId($treeNode->getId())
            ->setSiterootId($treeNode->getSiterootId())
            ->setTypeId($treeNode->getTypeId())
            ->setType($treeNode->getType())
            ->setTree($this)
            ->setParentNode($treeNode->getParentNode())
            ->setInNavigation($treeNode->getInNavigation())
            ->setSort($treeNode->getSort())
            ->setSortMode($treeNode->getSortMode())
            ->setSortDir($treeNode->getSortDir())
            ->setCreatedAt($treeNode->getCreatedAt())
            ->setCreateUserId($treeNode->getCreateUserId())
            ->setAttributes($treeNode->getAttributes());

        return $contentNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
    {
        return $this->tree->getRoot();
    }

    /**
     * @var ContentTreeNode[]
     */
    private $contentNodes = array();

    /**
     * @return array
     */
    public function getNodes()
    {
        return $this->contentNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (!isset($this->contentNodes[$id])) {
            $treeNode = $this->tree->get($id);
            $contentNode = null;
            if ($treeNode) {
                $contentNode = $this->createContentTreeNodeFromTreeNode($treeNode);
            }

            $this->contentNodes[$id] = $contentNode;
        }

        return $this->contentNodes[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return $this->tree->has($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren(TreeNodeInterface $node)
    {
        $children = [];
        foreach ($this->tree->getChildren($node) as $childNode) {
            $children[] = $this->get($childNode->getId());
        }

        return $children;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren(TreeNodeInterface $node)
    {
        return $this->tree->hasChildren($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(TreeNodeInterface $node)
    {
        $parentNode = $node->getParentNode();
        if (!$parentNode) {
            return null;
        }

        return $this->get($parentNode->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getIdPath(TreeNodeInterface $node)
    {
        return $this->tree->getIdPath($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(TreeNodeInterface $node)
    {
        $path = [];
        foreach ($this->tree->getPath($node) as $pathNode) {
            $path[] = $this->get($pathNode->getId());
        }

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot(TreeNodeInterface $node)
    {
        return $this->tree->isRoot($node);
    }

    /**
     * {@inheritdoc}
     */
    public function isChildOf(TreeNodeInterface $childNode, TreeNodeInterface $parentNode)
    {
        return $this->tree->isChildOf($childNode, $parentNode);
    }

    /**
     * {@inheritdoc}
     */
    public function isParentOf(TreeNodeInterface $parentNode, TreeNodeInterface $childNode)
    {
        return $this->tree->isChildOf($childNode, $parentNode);
    }

    /**
     * {@inheritdoc}
     */
    public function isInstance(TreeNodeInterface $node)
    {
        return $this->tree->isInstance($node);
    }

    /**
     * {@inheritdoc}
     */
    public function isInstanceMaster(TreeNodeInterface $node)
    {
        return $this->tree->isInstanceMaster($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getInstances(TreeNodeInterface $node)
    {
        return $this->tree->getInstances($node);
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished(TreeNodeInterface $node, $language = null)
    {
        return $this->tree->isPublished($node, $language ?: $this->language);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedLanguages(TreeNodeInterface $node)
    {
        return $this->tree->getPublishedLanguages($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersion(TreeNodeInterface $node, $language = null)
    {
        return $this->tree->getPublishedVersion($node, $language ?: $this->language);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedAt(TreeNodeInterface $node, $language = null)
    {
        return $this->tree->getPublishedAt($node, $language ?: $this->language);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersions(TreeNodeInterface $node)
    {
        return $this->tree->getPublishedVersions($node);
    }

    /**
     * {@inheritdoc}
     */
    public function isAsync(TreeNodeInterface $node, $language = null)
    {
        return $this->tree->isAsync($node, $language ?: $this->language);
    }

    /**
     * {@inheritdoc}
     */
    public function findOnlineByTreeNode(TreeNodeInterface $node)
    {
        return $this->tree->findOnlineByTreeNode($node);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneOnlineByTreeNodeAndLanguage(TreeNodeInterface $node, $language = null)
    {
        return $this->tree->findOneOnlineByTreeNodeAndLanguage($node, $language ?: $this->language);
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(TreeNodeInterface $node, $language)
    {
        return $this->mediator->getContentDocument($node)->getVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getByTypeId($typeId, $type = null)
    {
        return $this->tree->getByTypeId($typeId, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function hasByTypeId($typeId, $type = null)
    {
        return $this->tree->hasByTypeId($typeId, $type);
    }

    /**
     * @var bool
     */
    private $preview = false;

    /**
     * @param bool $preview
     *
     * @return $this
     */
    public function setPreview($preview = true)
    {
        $this->preview = $preview;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isViewable(TreeNodeInterface $node, $language = null)
    {
        return $this->mediator->isViewable($node, $language ?: $this->language);
    }

    /**
     * {@inheritdoc}
     */
    public function isSluggable(TreeNodeInterface $node, $language = null)
    {
        return $this->mediator->isSluggable($node, $language ?: $this->language);
    }

    /**
     * {@inheritdoc}
     */
    public function hasViewableChildren(TreeNodeInterface $node, $language = null)
    {
        foreach ($this->getChildren($node) as $childNode) {
            if ($this->isViewable($childNode, $language)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(TreeNodeInterface $node)
    {
        return $this->mediator->getContentDocument($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getField(TreeNodeInterface $node, $field, $language = null)
    {
        return $this->mediator->getField($node, $field, $language ?: $this->language);
    }
}
