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
use Phlexible\Bundle\TreeBundle\Model\TreeIdentifier;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;
use Phlexible\Component\Identifier\IdentifiableInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Delegating content tree
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingContentTree implements ContentTreeInterface, \IteratorAggregate, IdentifiableInterface
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
     *
     * @return TreeIdentifier
     */
    public function getIdentifier()
    {
        return new TreeIdentifier($this->getSiterootId());
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

        $titles = array();
        foreach (array('de', 'en') as $language) {
            $titles[$language] = $this->mediator->getTitle($treeNode, 'navigation', $language);
        }
        $contentNode->setTitles($titles);

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
     * {@inheritdoc}
     */
    public function get($id)
    {
        $treeNode = $this->tree->get($id);
        $contentNode = $this->createContentTreeNodeFromTreeNode($treeNode);

        return $contentNode;
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
        $children = array();
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
        return $this->get($node->getParentNode()->getId());
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
        $path = array();
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
        return $this->mediator->getVersionedObject($node)->getVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getByTypeId($typeId, $type = null)
    {
        // TODO: Implement getByTypeId() method.
    }

    /**
     * {@inheritdoc}
     */
    public function hasByTypeId($typeId, $type = null)
    {
        // TODO: Implement hasByTypeId() method.
    }
}
