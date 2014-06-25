<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree\Dumper;

use Cocur\Slugify\Slugify;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\TreeBundle\Tree\Node\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeInterface;

/**
 * XML dumper
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlDumper
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var Slugify
     */
    private $slugifier;

    /**
     * @param ElementService $elementService
     */
    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
        $this->slugifier = new Slugify();
    }

    /**
     * @param TreeInterface $tree
     * @param Siteroot      $siteroot
     * @param string        $filename
     */
    public function dump(TreeInterface $tree, Siteroot $siteroot, $filename)
    {
        $dom = new \DOMDocument();
        $dom->formatOutput = true;

        $nodes = array();

        $contentTreeNode = $dom->createElement('contentTree');
        $dom->appendChild($contentTreeNode);

        $contentTreeIdAttr = $dom->createAttribute('id');
        $contentTreeIdAttr->value = (string) $siteroot->getId();
        $contentTreeNode->appendChild($contentTreeIdAttr);

        $siterootNode = $dom->createElement('siteroot');
        $contentTreeNode->appendChild($siterootNode);

        $urlsNode = $dom->createElement('urls');
        $siterootNode->appendChild($urlsNode);

        foreach ($siteroot->getUrls() as $url) {
            $urlNode = $dom->createElement('url', $url->getHostname());
            $urlsNode->appendChild($urlNode);

            $idAttr = $dom->createAttribute('id');
            $idAttr->value =  $url->getId();
            $urlNode->appendChild($idAttr);

            $languageAttr = $dom->createAttribute('language');
            $languageAttr->value =  $url->getLanguage();
            $urlNode->appendChild($languageAttr);

            $targetAttr = $dom->createAttribute('target');
            $targetAttr->value = $url->getTarget();
            $urlNode->appendChild($targetAttr);

            $defaultAttr = $dom->createAttribute('default');
            $defaultAttr->value = $url->isDefault() ? 1 : 0;
            $urlNode->appendChild($defaultAttr);
        }

        $specialTidsNode = $dom->createElement('specialTids');
        $siterootNode->appendChild($specialTidsNode);

        foreach ($siteroot->getAllSpecialTids() as $language => $specialTids) {
            foreach ($specialTids as $key => $specialTid) {
                $specialTidNode = $dom->createElement('specialTid', $specialTid);
                $specialTidsNode->appendChild($specialTidNode);

                $keyAttr = $dom->createAttribute('key');
                $keyAttr->value = $key;
                $specialTidNode->appendChild($keyAttr);

                $languageAttr = $dom->createAttribute('language');
                $languageAttr->value = $language;
                $specialTidNode->appendChild($languageAttr);
            }
        }

        $navigationsNode = $dom->createElement('navigations');
        $siterootNode->appendChild($navigationsNode);

        foreach ($siteroot->getNavigations() as $navigation) {
            $navigationNode = $dom->createElement('navigation');
            $navigationsNode->appendChild($navigationNode);

            $titleAttr = $dom->createAttribute('title');
            $titleAttr->value = $navigation->getTitle();
            $navigationNode->appendChild($titleAttr);

            $startTidAttr = $dom->createAttribute('startTreeId');
            $startTidAttr->value = $navigation->getStartTreeId();
            $navigationNode->appendChild($startTidAttr);

            $maxDepthAttr = $dom->createAttribute('maxDepth');
            $maxDepthAttr->value = $navigation->getMaxDepth();
            $navigationNode->appendChild($maxDepthAttr);
        }

        $treeNode = $nodes[null] = $dom->createElement('tree');
        $contentTreeNode->appendChild($treeNode);

        $rii = new \RecursiveIteratorIterator($tree->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $node) {
            /* @var $node TreeNodeInterface */

            $nodes[$node->getId()] = $nodeNode = $dom->createElement('node');
            $nodes[$node->getParentId()]->appendChild($nodeNode);

            $idAttr = $dom->createAttribute('id');
            $idAttr->value = $node->getId();
            $nodeNode->appendChild($idAttr);

            $parentIdAttr = $dom->createAttribute('parentId');
            $parentIdAttr->value = $node->getParentId();
            $nodeNode->appendChild($parentIdAttr);

            $typeAttr = $dom->createAttribute('type');
            $typeAttr->value = $node->getType();
            $nodeNode->appendChild($typeAttr);

            $typeIdAttr = $dom->createAttribute('typeId');
            $typeIdAttr->value = $node->getTypeId();
            $nodeNode->appendChild($typeIdAttr);

            $sortAttr = $dom->createAttribute('sort');
            $sortAttr->value = $node->getSort();
            $nodeNode->appendChild($sortAttr);

            $sortModeAttr = $dom->createAttribute('sortMode');
            $sortModeAttr->value = $node->getSortMode();
            $nodeNode->appendChild($sortModeAttr);

            $sortDirAttr = $dom->createAttribute('sortDir');
            $sortDirAttr->value = $node->getSortDir();
            $nodeNode->appendChild($sortDirAttr);

            $createUserIdAttr = $dom->createAttribute('createUserId');
            $createUserIdAttr->value = $node->getCreateUserId();
            $nodeNode->appendChild($createUserIdAttr);

            $createdAtAttr = $dom->createAttribute('createdAt');
            $createdAtAttr->value = $node->getCreatedAt()->format('Y-m-d H:i:s');
            $nodeNode->appendChild($createdAtAttr);

            $attributesNode = $dom->createElement('attributes');
            $nodeNode->appendChild($attributesNode);

            foreach ($node->getAttributes() as $key => $value) {
                $attributeKeyAttr = $dom->createAttribute($key);
                $attributeKeyAttr->value = $value;
                $attributesNode->appendChild($attributeKeyAttr);
            }

            $versionsNode = $dom->createElement('versions');
            $nodeNode->appendChild($versionsNode);

            $titlesNode = $dom->createElement('titles');
            $nodeNode->appendChild($titlesNode);

            $slugsNode = $dom->createElement('slugs');
            $nodeNode->appendChild($slugsNode);

            $languages = $tree->getLanguages($node);

            foreach ($languages as $language) {
                $version = $tree->getVersion($node, $language);

                $versionNode = $dom->createElement('version', $version);
                $versionsNode->appendChild($versionNode);

                $languageAttr = $dom->createAttribute('language');
                $languageAttr->value = $language;
                $versionNode->appendChild($languageAttr);

                $element = $this->elementService->findElement($node->getTypeId());
                $elementVersion = $this->elementService->findElementVersion($element, $version);
                $title = $elementVersion->getNavigationTitle($language);

                $slugNode = $dom->createElement('slug', $this->slugifier->slugify($title));
                $slugsNode->appendChild($slugNode);

                $languageAttr = $dom->createAttribute('language');
                $languageAttr->value = $language;
                $slugNode->appendChild($languageAttr);

                $titleNode = $dom->createElement('title');
                $titlesNode->appendChild($titleNode);

                $titleNode->appendChild($dom->createTextNode($title));

                $languageAttr = $dom->createAttribute('language');
                $languageAttr->value = $language;
                $titleNode->appendChild($languageAttr);
            }
        }

        $dom->save($filename);
    }
}
