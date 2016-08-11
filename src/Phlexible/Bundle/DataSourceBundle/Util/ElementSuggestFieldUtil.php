<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Util;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
use Phlexible\Bundle\DataSourceBundle\GarbageCollector\ValuesCollection;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;

/**
 * Utility class for suggest fields.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementSuggestFieldUtil implements Util
{
    /**
     * @var MetaSetManagerInterface
     */
    private $metaSetManager;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var ElementSourceManagerInterface
     */
    private $elementSourceManager;

    /**
     * @var ContentTreeManagerInterface
     */
    private $treeManager;

    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @var string
     */
    private $separatorChar;

    /**
     * @param MetaSetManagerInterface       $metaSetManager
     * @param ElementService                $elementService
     * @param ElementSourceManagerInterface $elementSourceManager
     * @param ContentTreeManagerInterface   $treeManager
     * @param TeaserManagerInterface        $teaserManager
     * @param string                        $separatorChar
     */
    public function __construct(
        MetaSetManagerInterface $metaSetManager,
        ElementService $elementService,
        ElementSourceManagerInterface $elementSourceManager,
        ContentTreeManagerInterface $treeManager,
        TeaserManagerInterface $teaserManager,
        $separatorChar
    ) {
        $this->metaSetManager = $metaSetManager;
        $this->elementService = $elementService;
        $this->elementSourceManager = $elementSourceManager;
        $this->treeManager = $treeManager;
        $this->teaserManager = $teaserManager;
        $this->separatorChar = $separatorChar;
    }

    /**
     * Fetch all data source values used in any element versions.
     *
     * @param DataSourceValueBag $valueBag
     *
     * @return ValuesCollection
     */
    public function fetchValues(DataSourceValueBag $valueBag)
    {
        $metaSets = $this->metaSetManager->findAll();

        $fields = array();
        foreach ($metaSets as $metaSet) {
            foreach ($metaSet->getFields() as $field) {
                if ($field->getOptions() === $valueBag->getDatasource()->getId()) {
                    $fields[] = $field;
                }
            }
        }

        $nodes = array();
        foreach ($this->elementSourceManager->findAll() as $elementSource) {
            if ($elementSource->getType() !== 'full' && $elementSource->getType() !== 'structure' && $elementSource->getType() !== 'part') {
                continue;
            }

            $elementtype = $this->elementSourceManager->findElementtype($elementSource->getElementtypeId());

            $rii = new \RecursiveIteratorIterator($elementtype->getStructure()->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($rii as $node) {
                if ($node->getType() === 'suggest') {
                    if (!empty($node->getConfigurationValue('suggest_source'))) {
                        $source = $node->getConfigurationValue('suggest_source');
                        if ($source === $valueBag->getDatasource()->getId()) {
                            $nodes[] = array($elementtype, $node);
                        }
                    }
                }
            }
        }

        $values = new ValuesCollection();

        foreach ($nodes as $nodeRow) {
            $elementtype = $nodeRow[0];
            $suggestNode = $nodeRow[1];
            foreach ($this->elementService->findElementsByElementtype($elementtype) as $element) {
                foreach ($this->elementService->findElementVersions($element) as $elementVersion) {
                    $elementStructure = $this->elementService->findElementStructure($elementVersion, $valueBag->getLanguage());

                    foreach ($elementStructure->getValues() as $value) {
                        if ($value->getDsId() === $suggestNode->getDsId()) {
                            if ($this->isOnline($element, $elementVersion, $elementtype, $valueBag->getLanguage())) {
                                $values->addActiveValues($value->getValue());
                            } else {
                                $values->addInactiveValues($value->getValue());
                            }
                        }
                    }

                    $rii = new \RecursiveIteratorIterator($elementStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
                    foreach ($rii as $structure) {
                        foreach ($structure->getValues() as $value) {
                            if ($value->getDsId() === $suggestNode->getDsId()) {
                                if ($this->isOnline($element, $elementVersion, $elementtype, $valueBag->getLanguage())) {
                                    $values->addActiveValues($value->getValue());
                                } else {
                                    $values->addInactiveValues($value->getValue());
                                }
                            }
                        }
                    }
                }
            }
        }

        return $values;
    }

    /**
     * @param Element        $element
     * @param ElementVersion $elementVersion
     * @param Elementtype    $elementtype
     *
     * @return bool
     */
    private function isOnline(Element $element, ElementVersion $elementVersion, Elementtype $elementtype, $language)
    {
        if ($elementtype->getType() === Elementtype::TYPE_PART) {
            foreach ($this->teaserManager->findBy(array('typeId' => $element->getEid())) as $teaser) {
                if ($this->teaserManager->getPublishedVersion($teaser, $language) === $elementVersion->getVersion()) {
                    return true;
                }
            }
        } else {
            foreach ($this->treeManager->findAll() as $tree) {
                foreach ($tree->getByTypeId($element->getEid()) as $node) {
                    //echo $node->getId()." ".$language." | ".$tree->getPublishedVersion($node, $language)." => ".$elementVersion->getVersion().PHP_EOL;
                    if ($tree->getPublishedVersion($node, $language) === $elementVersion->getVersion()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
