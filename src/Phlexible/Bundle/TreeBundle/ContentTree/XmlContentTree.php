<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Phlexible\Bundle\SiterootBundle\Entity\Navigation;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\TreeBundle\Model\TreeIdentifier;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;
use Phlexible\Component\Identifier\IdentifiableInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * XML content tree
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlContentTree implements ContentTreeInterface, \IteratorAggregate, IdentifiableInterface
{
    /**
     * @var int
     */
    private $rootId;

    /**
     * @var array
     */
    private $nodes = array();

    /**
     * @var array
     */
    private $childNodes = array();

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
     *
     * @return \Phlexible\Bundle\TreeBundle\Model\TreeIdentifier
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
     * @return array
     */
    public function getSpecialTids()
    {
        return $this->getSiteroot()->getSpecialTids();
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
        $siteroot->setContentChannels(array(1 => true));

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

        $specialTids = array();
        foreach ($specialTidElements as $specialTidElement) {
            /* @var $specialTidElement \DOMElement */
            $key = $specialTidElement->getAttribute('key');
            $language = $specialTidElement->getAttribute('language') ? : null;
            $specialTids[$language][$key] = (int) $specialTidElement->textContent;
        }
        $siteroot->setAllSpecialTids($specialTids);

        return $siteroot;
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
            throw new \Exception('Root node not found.');
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
            throw new \Exception("$id not found");
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
    public function getChildren($node)
    {
        if ($node instanceof TreeNodeInterface) {
            $id = $node->getId();
        } else {
            $id = (int) $node;
        }

        if (isset($this->childNodes[$id])) {
            return $this->childNodes[$id];
        }

        $elements = $this->xpath->query("//node[@id=$id]/node");

        if (!$elements->length) {
            return array();
        }

        $childNodes = $this->mapNodes($elements);
        $this->childNodes[$id] = $childNodes;

        return $childNodes;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren($node)
    {
        return count($this->getChildren($node)) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent($node)
    {
        if (!$node instanceof TreeNodeInterface) {
            $node = $this->get($node);
        }

        $parentId = $node->getParentId();

        if ($parentId === null) {
            return null;
        }

        return $this->get($parentId);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdPath($node)
    {
        return array_keys($this->getPath($node));
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($node)
    {
        if ($node instanceof TreeNodeInterface) {
            $nodeId = $node->getId();
        } else {
            $nodeId = (int) $node;
        }

        $elements = $this->xpath->query("//node[@id=$nodeId]");
        if (!$elements->length) {
            return array();
        }

        $element = $elements->item(0);
        $elementPath = $this->xpath->query($element->getNodePath());

        $path = array();
        foreach ($elementPath as $element) {
            $path[] = $this->get($element->attributes->item(0)->value);
        }

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot($node)
    {
        if ($node instanceof TreeNodeInterface) {
            $id = $node->getId();
        } else {
            $id = (int) $node;
        }

        return $this->getRoot()->getId() === $id;
    }

    /**
     * {@inheritdoc}
     */
    public function isChildOf($childId, $parentId)
    {
        if ($childId instanceof TreeNodeInterface) {
            $childId = $childId->getId();
        }

        if ($parentId instanceof TreeNodeInterface) {
            $parentId = $parentId->getId();
        }

        return $this->xpath->query("//node[@id=$parentId]//node[@id=$childId]")->length > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isParentOf($parentId, $childId)
    {
        return $this->isChildOf($childId, $parentId);
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

        $languages = array();
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

        $versions = array();
        foreach ($elements as $element) {
            $language = $element->attributes->item(0)->value;
            $versions[$language] = (int) $element->textContent;
        }

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

        $elements = $this->xpath->query("//node[@id=$id]/versions/version[@language=\"$language\"]");
        if (!$elements->length) {
            throw new \Exception("language $language not found");
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
        $nodes = array();

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
        $attributes = array();

        $titles = array();
        $titlesElement = $element->getElementsByTagName('titles')->item(0);
        foreach ($titlesElement->getElementsByTagName('title') as $titleNode) {
            $titles[$titleNode->getAttribute('language')] = $titleNode->textContent;
        }

        $slugs = array();
        $slugsElement = $element->getElementsByTagName('slugs')->item(0);
        foreach ($slugsElement->getElementsByTagName('slug') as $slugNode) {
            $slugs[$slugNode->getAttribute('language')] = $slugNode->textContent;
        }

        $versions = array();
        $versionsElement = $element->getElementsByTagName('versions')->item(0);
        foreach ($versionsElement->getElementsByTagName('version') as $versionNode) {
            $versions[$versionNode->getAttribute('language')] = (int) $versionNode->textContent;
        }

        $node = new ContentTreeNode();
        $node
            ->setTree($this)
            ->setId((int) $element->getAttribute('id'))
            ->setParentId($element->getAttribute('parentId') ? (int) $element->getAttribute('parentId') : null)
            ->setType((string) $element->getAttribute('type'))
            ->setTypeId((int) $element->getAttribute('typeId'))
            ->setAttributes($attributes)
            ->setSort((string) $element->getAttribute('sort'))
            ->setSortMode((string) $element->getAttribute('sortMode'))
            ->setSortDir((string) $element->getAttribute('sortDir'))
            ->setTitles($titles)
            ->setSlugs($slugs)
            ->setVersions($versions)
            ->setCreatedAt(new \DateTime((string) $element->getAttribute('createdAt')))
            ->setCreateUserId((string) $element->getAttribute('createUserId'));

        $this->nodes[$node->getId()] = $node;

        if ($node->getParentId() === null) {
            $this->rootId = $node->getId();
        }

        return $node;
    }

    /**
     * @param TreeNodeInterface|int $node
     *
     * @return bool
     */
    public function isInstance($node)
    {
        return false;
    }

    /**
     * @param TreeNodeInterface|int $node
     *
     * @return bool
     */
    public function isInstanceMaster($node)
    {
        return false;
    }

    /**
     * @param TreeNodeInterface|int $node
     *
     * @return TreeNodeInterface[]
     */
    public function getInstances($node)
    {
        return array();
    }
}
