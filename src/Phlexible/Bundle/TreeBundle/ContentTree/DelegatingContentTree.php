<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Phlexible\Component\Identifier\IdentifiableInterface;
use Phlexible\Bundle\SiterootBundle\Entity\Navigation;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\TreeBundle\Tree\Node\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeIdentifier;
use Phlexible\Bundle\TreeBundle\Tree\TreeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * XML content tree
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlContentTree implements TreeInterface, \IteratorAggregate, IdentifiableInterface
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
     * @param TreeInterface $tree
     * @param Siteroot      $siteroot
     */
    public function __construct(TreeInterface $tree, Siteroot $siteroot)
    {
        $this->tree = $tree;
        $this->siteroot = $siteroot;
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
    public function getSpecialTids()
    {
        return $this->getSiteroot()->getSpecialTids();
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
        return $this->tree->get($id);
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
        return $this->tree->hasChildren($node);
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
        return $this->tree->getParent($node);
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
        return $this->tree->getPath($node);
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
}
