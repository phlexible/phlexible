<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree\Dumper;

use Cocur\Slugify\Slugify;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\TreeBundle\Mediator\MediatorInterface;
use Phlexible\Bundle\TreeBundle\Model\StateManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * XML dumper
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlDumper
{
    /**
     * @var StateManagerInterface
     */
    private $stateManager;

    /**
     * @var Slugify
     */
    private $slugifier;

    /**
     * @var MediatorInterface
     */
    private $mediator;

    /**
     * @param StateManagerInterface $stateManager
     * @param MediatorInterface     $mediator
     */
    public function __construct(StateManagerInterface $stateManager, MediatorInterface $mediator)
    {
        $this->stateManager = $stateManager;
        $this->mediator = $mediator;

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

        foreach ($siteroot->getSpecialTids() as $specialTid) {
            $specialTidNode = $dom->createElement('specialTid', $specialTid['treeId']);
            $specialTidsNode->appendChild($specialTidNode);

            $keyAttr = $dom->createAttribute('name');
            $keyAttr->value = $specialTid['name'];
            $specialTidNode->appendChild($keyAttr);

            if ($specialTid['language']) {
                $languageAttr = $dom->createAttribute('language');
                $languageAttr->value = $specialTid['language'];
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

        $nodes = [];

        $treeNode = $nodes[null] = $dom->createElement('tree');
        $contentTreeNode->appendChild($treeNode);

        $rii = new \RecursiveIteratorIterator($tree->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $node) {
            /* @var $node TreeNodeInterface */

            $nodes[$node->getId()] = $nodeNode = $dom->createElement('node');
            if ($node->getParentNode()) {
                $parentId = $node->getParentNode()->getId();
            } else {
                $parentId = null;
            }
            $nodes[$parentId]->appendChild($nodeNode);

            $idAttr = $dom->createAttribute('id');
            $idAttr->value = $node->getId();
            $nodeNode->appendChild($idAttr);

            if ($parentId) {
                $parentIdAttr = $dom->createAttribute('parentId');
                $parentIdAttr->value = $node->getParentNode()->getId();
                $nodeNode->appendChild($parentIdAttr);
            }

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

            if ($node->getAttributes()) {
                foreach ($node->getAttributes() as $key => $value) {
                    $attributeKeyAttr = $dom->createAttribute($key);
                    $attributeKeyAttr->value = $value;
                    $attributesNode->appendChild($attributeKeyAttr);
                }
            }

            $versionsNode = $dom->createElement('versions');
            $nodeNode->appendChild($versionsNode);

            $titlesNode = $dom->createElement('titles');
            $nodeNode->appendChild($titlesNode);

            $slugsNode = $dom->createElement('slugs');
            $nodeNode->appendChild($slugsNode);

            $languages = $this->stateManager->getPublishedLanguages($node);

            foreach ($languages as $language) {
                $version = $this->stateManager->getPublishedVersion($node, $language);

                $versionNode = $dom->createElement('version', $version);
                $versionsNode->appendChild($versionNode);

                $languageAttr = $dom->createAttribute('language');
                $languageAttr->value = $language;
                $versionNode->appendChild($languageAttr);

                $title = $this->mediator->getField($node, 'navigation', $language);

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

        $filesystem = new Filesystem();
        $filesystem->mkdir(dirname($filename));

        $dom->save($filename);
    }
}
