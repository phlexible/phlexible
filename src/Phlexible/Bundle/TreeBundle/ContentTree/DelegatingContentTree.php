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
     * @param TreeInterface     $tree
     * @param Siteroot          $siteroot
     * @param MediatorInterface $mediator
     */
    public function __construct(TreeInterface $tree, Siteroot $siteroot, MediatorInterface $mediator)
    {
        $this->tree = $tree;
        $this->siteroot = $siteroot;
        $this->mediator = $mediator;
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
        return $this->getSiteroot()->getSpecialTidsForLanguage($language);
    }

    /**
     * {@inheritdoc}
     */
    public function createContentTreeNodeFromTreeNode(TreeNodeInterface $treeNode)
    {
        $contentNode = new ContentTreeNode();
        $contentNode
            ->setId($treeNode->getId())
            ->setTypeId($treeNode->getTypeId())
            ->setType($treeNode->getType())
            ->setTree($this)
            ->setParentId($treeNode->getParentId())
            ->setInNavigation($treeNode->getInNavigation())
            ->setSort($treeNode->getSort())
            ->setSortMode($treeNode->getSortMode())
            ->setSortDir($treeNode->getSortDir());

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
    public function getChildren($node)
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
    public function hasChildren($node)
    {
        return $this->tree->hasChildren($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent($node)
    {
        return $this->get($node->getParentId());
    }

    /**
     * {@inheritdoc}
     */
    public function getIdPath($node)
    {
        return $this->tree->getIdPath($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($node)
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
    public function isRoot($node)
    {
        return $this->tree->isRoot($node);
    }

    /**
     * {@inheritdoc}
     */
    public function isChildOf($childId, $parentId)
    {
        return $this->tree->isChildOf($childId, $parentId);
    }

    /**
     * {@inheritdoc}
     */
    public function isParentOf($parentId, $childId)
    {
        return $this->tree->isChildOf($childId, $parentId);
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguages($node)
    {
        if ($node instanceof TreeNodeInterface) {
            $id = $node->getId();
        } else {
            $id = $node;
        }

        $languages = array();

        return $languages;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersions($node)
    {
        if ($node instanceof TreeNodeInterface) {
            $id = $node->getId();
        } else {
            $id = $node;
        }

        $versions = array();

        return $versions;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion($node, $language)
    {
        if ($node instanceof TreeNodeInterface) {
            $id = $node->getId();
        } else {
            $id = $node;
        }

        $version = 0;

        return $version;
    }

    /**
     * @param TreeNodeInterface|int $node
     *
     * @return bool
     */
    public function isInstance($node)
    {
        return $this->tree->isInstance($node);
    }

    /**
     * @param TreeNodeInterface|int $node
     *
     * @return bool
     */
    public function isInstanceMaster($node)
    {
        return $this->tree->isInstanceMaster($node);
    }

    /**
     * @param TreeNodeInterface|int $node
     *
     * @return TreeNodeInterface[]
     */
    public function getInstances($node)
    {
        return $this->tree->getInstances($node);
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished(TreeNodeInterface $node, $language)
    {
        return $this->tree->isPublished($node, $language);
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
    public function getPublishedVersion(TreeNodeInterface $node, $language)
    {
        return $this->tree->getPublishedVersion($node, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedVersions(TreeNodeInterface $node)
    {
        return $this->tree->getPublishedVersions($node);
    }
}
