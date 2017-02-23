<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Phlexible\Bundle\SiterootBundle\Entity\Navigation;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\TreeBundle\Entity\TreeNodeOnline;
use Phlexible\Bundle\TreeBundle\Exception\InvalidArgumentException;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;

/**
 * XML content tree.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlContentTree implements ContentTreeInterface, \IteratorAggregate
{
    /**
     * @var int
     */
    private $rootId;

    /**
     * @var array
     */
    private $nodes = [];

    /**
     * @var array
     */
    private $childNodes = [];

    /**
     * @var \DOMDocument
     */
    private $dom;

    /**
     * @var \DOMXPath
     */
    private $xpath;

    /**
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->dom = new \DOMDocument();
        $this->dom->recover = false;
        $this->dom->resolveExternals = false;
        $this->dom->strictErrorChecking = false;
        $this->dom->load($filename);
        $this->xpath = new \DOMXPath($this->dom);
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
        return $this->xpath->query('/contentTree/siteroot')->item(0)->getAttribute('id');
    }

    /**
     * {@inheritdoc}
     */
    public function getSiteroot()
    {
        return $this->mapSiteroot($this->xpath->query('/contentTree/siteroot')->item(0));
    }

    /**
     * {@inheritdoc}
     */
    public function isDefaultSiteroot()
    {
        $attributes = $this->xpath->query('/contentTree/siteroot')->item(0)->attributes;
        if (!$attributes->length) {
            // TODO: false
            return true;
        }

        return (bool) $attributes->item(0)->value;
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
     * @param string $language
     *
     * @return array
     */
    public function getSpecialTids($language = null)
    {
        return $this->getSiteroot()->getSpecialTidsForLanguage($language);
    }

    /**
     * @param \DOMElement $element
     *
     * @return Url
     */
    private function mapSiteroot(\DOMElement $element)
    {
        $urlElements = $element->getElementsByTagName('url');
        $navigationElements = $element->getElementsByTagName('navigation');
        $specialTidElements = $element->getElementsByTagName('specialTid');

        $siteroot = new Siteroot();

        foreach ($urlElements as $urlElement) {
            /* @var $urlElement \DOMElement */
            $url = new Url();
            $url
                ->setSiteroot($siteroot)
                ->setId((bool) $urlElement->getAttribute('id'))
                ->setDefault((bool) $urlElement->getAttribute('default'))
                ->setHostname((string) $urlElement->textContent)
                ->setLanguage((bool) $urlElement->getAttribute('language'))
                ->setTarget((bool) $urlElement->getAttribute('target'));
            $siteroot->addUrl($url);
        }

        foreach ($navigationElements as $navigationElement) {
            /* @var $navigationElement \DOMElement */
            $navigation = new Navigation();
            $navigation
                ->setSiteroot($siteroot)
                ->setTitle((string) $navigationElement->getAttribute('title'))
                ->setStartTreeId((int) $navigationElement->getAttribute('startTreeId'))
                ->setMaxDepth((int) $navigationElement->getAttribute('maxDepth'));
            $siteroot->addNavigation($navigation);
        }

        $specialTids = [];
        foreach ($specialTidElements as $specialTidElement) {
            /* @var $specialTidElement \DOMElement */
            $name = $specialTidElement->getAttribute('name');
            $language = $specialTidElement->getAttribute('language') ?: null;
            $specialTids[] = ['name' => $name, 'language' => $language, 'treeId' => (int) $specialTidElement->textContent];
        }
        $siteroot->setSpecialTids($specialTids);

        return $siteroot;
    }

    /**
     * {@inheritdoc}
     */
    public function createContentTreeNodeFromTreeNode(TreeNodeInterface $treeNode)
    {
        $contentNode = new ContentTreeNode();
        $contentNode
            ->setTypeId($treeNode->getTypeId())
            ->setType($treeNode->getType())
            ->setTree($this)
            ->setParentNode($treeNode->getParentNode())
            ->setInNavigation($treeNode->getInNavigation())
            ->setSort($treeNode->getSort())
            ->setSortMode($treeNode->getSortMode())
            ->setSortDir($treeNode->getSortDir())
            ->setTitles(['de' => 'bla', 'en' => 'blubb']);

        return $contentNode;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
    {
        if ($this->rootId) {
            return $this->nodes[$this->rootId];
        }

        $elements = $this->xpath->query('/contentTree/tree/node[1]');

        if (!$elements->length) {
            throw new InvalidArgumentException('Root node not found.');
        }

        $element = $elements->item(0);

        return $this->mapNode($element);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (isset($this->nodes[$id])) {
            return $this->nodes[$id];
        }

        $elements = $this->xpath->query("//node[@id=$id]");

        if (!$elements->length) {
            throw new InvalidArgumentException("$id not found");
        }

        $element = $elements->item(0);

        return $this->mapNode($element);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        if (isset($this->nodes[$id])) {
            return true;
        }

        if ($id instanceof TreeNodeInterface) {
            $id = $id->getId();
        }

        return $this->get($id) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren(TreeNodeInterface $node)
    {
        if (isset($this->childNodes[$node->getId()])) {
            return $this->childNodes[$node->getId()];
        }

        $elements = $this->xpath->query("//node[@id={$node->getId()}]/node");

        if (!$elements->length) {
            return [];
        }

        $childNodes = $this->mapNodes($elements);
        $this->childNodes[$node->getId()] = $childNodes;

        return $childNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren(TreeNodeInterface $node)
    {
        return count($this->getChildren($node)) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(TreeNodeInterface $node)
    {
        return $this->getParent($node);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdPath(TreeNodeInterface $node)
    {
        return array_keys($this->getPath($node));
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(TreeNodeInterface $node)
    {
        $elements = $this->xpath->query("//node[@id={$node->getId()}]");
        if (!$elements->length) {
            return [];
        }

        $element = $elements->item(0);
        $elementPath = $this->xpath->query($element->getNodePath());

        $path = [];
        foreach ($elementPath as $element) {
            $path[] = $this->get($element->attributes->item(0)->value);
        }

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot(TreeNodeInterface $node)
    {
        return $this->getRoot()->getId() === $node->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function isChildOf(TreeNodeInterface $childNode, TreeNodeInterface $parentNode)
    {
        return $this->xpath->query("//node[@id={$parentNode->getId()}]//node[@id={$childNode->getId()}]")->length > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isParentOf(TreeNodeInterface $parentNode, TreeNodeInterface $childNode)
    {
        return $this->isChildOf($childNode, $parentNode);
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

        $elements = $this->xpath->query("//node[@id=$id]/versions/version");

        $languages = [];
        foreach ($elements as $element) {
            $languages[] = $element->attributes->item(0)->value;
        }

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

        $elements = $this->xpath->query("//node[@id=$id]/versions/version");

        $versions = [];
        foreach ($elements as $element) {
            $language = $element->attributes->item(0)->value;
            $versions[$language] = (int) $element->textContent;
        }

        return $versions;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(TreeNodeInterface $node, $language)
    {
        $elements = $this->xpath->query("//node[@id={$node->getId()}]/versions/version[@language=\"$language\"]");
        if (!$elements->length) {
            throw new InvalidArgumentException("language $language not found");
        }
        $version = (int) $elements->item(0)->textContent;

        return $version;
    }

    /**
     * @param \DOMNodeList $elements
     *
     * @return TreeNodeInterface[]
     */
    private function mapNodes(\DOMNodeList $elements)
    {
        $nodes = [];

        foreach ($elements as $element) {
            $node = $this->mapNode($element);
            $nodes[$node->getId()] = $node;
        }

        return $nodes;
    }

    /**
     * @param \DOMElement $element
     *
     * @return \Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface
     */
    private function mapNode(\DOMElement $element)
    {
        $attributes = [];

        $titles = [];
        $titlesElement = $element->getElementsByTagName('titles')->item(0);
        foreach ($titlesElement->getElementsByTagName('title') as $titleNode) {
            $titles[$titleNode->getAttribute('language')] = $titleNode->textContent;
        }

        $slugs = [];
        $slugsElement = $element->getElementsByTagName('slugs')->item(0);
        foreach ($slugsElement->getElementsByTagName('slug') as $slugNode) {
            $slugs[$slugNode->getAttribute('language')] = $slugNode->textContent;
        }

        $versions = [];
        $versionsElement = $element->getElementsByTagName('versions')->item(0);
        foreach ($versionsElement->getElementsByTagName('version') as $versionNode) {
            $versions[$versionNode->getAttribute('language')] = (int) $versionNode->textContent;
        }

        $node = new ContentTreeNode();
        $node
            ->setTree($this)
            ->setId((int) $element->getAttribute('id'))
            ->setParentNode($element->getAttribute('parentId') ? (int) $element->getAttribute('parentId') : null)
            ->setType((string) $element->getAttribute('type'))
            ->setTypeId((int) $element->getAttribute('typeId'))
            ->setAttributes($attributes)
            ->setSort((string) $element->getAttribute('sort'))
            ->setSortMode((string) $element->getAttribute('sortMode'))
            ->setSortDir((string) $element->getAttribute('sortDir'))
            ->setTitles($titles)
            ->setSlugs($slugs)
            //->setVersions($versions)
            ->setCreatedAt(new \DateTime((string) $element->getAttribute('createdAt')))
            ->setCreateUserId((string) $element->getAttribute('createUserId'));

        $this->nodes[$node->getId()] = $node;

        if ($node->getParentNode() === null) {
            $this->rootId = $node->getId();
        }

        return $node;
    }

    /**
     * @param TreeNodeInterface $node
     *
     * @return bool
     */
    public function isInstance(TreeNodeInterface $node)
    {
        return false;
    }

    /**
     * @param TreeNodeInterface $node
     *
     * @return bool
     */
    public function isInstanceMaster(TreeNodeInterface $node)
    {
        return false;
    }

    /**
     * @param TreeNodeInterface $node
     *
     * @return TreeNodeInterface[]
     */
    public function getInstances(TreeNodeInterface $node)
    {
        return [];
    }

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return bool
     */
    public function isPublished(TreeNodeInterface $node, $language)
    {
        // TODO: Implement isPublished() method.
    }

    /**
     * @param TreeNodeInterface $node
     *
     * @return array
     */
    public function getPublishedLanguages(TreeNodeInterface $node)
    {
        // TODO: Implement getPublishedLanguages() method.
    }

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return int|null
     */
    public function getPublishedVersion(TreeNodeInterface $node, $language)
    {
        // TODO: Implement getPublishedVersion() method.
    }

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return \DateTime|null
     */
    public function getPublishedAt(TreeNodeInterface $node, $language)
    {
        // TODO: Implement getPublishedAt() method.
    }

    /**
     * @param TreeNodeInterface $node
     *
     * @return array
     */
    public function getPublishedVersions(TreeNodeInterface $node)
    {
        // TODO: Implement getPublishedVersions() method.
    }

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return bool
     */
    public function isAsync(TreeNodeInterface $node, $language)
    {
        // TODO: Implement isAsync() method.
    }

    /**
     * @param TreeNodeInterface $node
     *
     * @return TreeNodeOnline[]
     */
    public function findOnlineByTreeNode(TreeNodeInterface $node)
    {
        // TODO: Implement findOnlineByTreeNode() method.
    }

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return TreeNodeOnline
     */
    public function findOneOnlineByTreeNodeAndLanguage(TreeNodeInterface $node, $language)
    {
        // TODO: Implement findOneOnlineByTreeNodeAndLanguage() method.
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

    /**
     * {@inheritdoc}
     */
    public function isViewable(TreeNodeInterface $node)
    {
        return false;
    }
}
