<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Util;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Model\ElementSourceManagerInterface;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureIterator;
use Phlexible\Component\MetaSet\Model\MetaDataManagerInterface;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;

/**
 * Utility class for suggest fields.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SuggestFieldUtil
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
     * @var string
     */
    private $separatorChar;

    /**
     * @param MetaSetManagerInterface       $metaSetManager
     * @param ElementService                $elementService
     * @param ElementSourceManagerInterface $elementSourceManager
     * @param string                        $separatorChar
     */
    public function __construct(MetaSetManagerInterface $metaSetManager, ElementService $elementService, ElementSourceManagerInterface $elementSourceManager, $separatorChar)
    {
        $this->metaSetManager = $metaSetManager;
        $this->elementService = $elementService;
        $this->elementSourceManager = $elementSourceManager;
        $this->separatorChar = $separatorChar;
    }

    /**
     * Fetch all data source values used in any element versions.
     *
     * @param DataSourceValueBag $valueBag
     *
     * @return array
     */
    public function fetchUsedValues(DataSourceValueBag $valueBag)
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

        $values = array();

        foreach ($nodes as $nodeRow) {
            $elementtype = $nodeRow[0];
            $suggestNode = $nodeRow[1];
            foreach ($this->elementService->findElementsByElementtype($elementtype) as $element) {
                foreach ($this->elementService->findElementVersions($element) as $elementVersion) {
                    $elementStructure = $this->elementService->findElementStructure($elementVersion, $valueBag->getLanguage());

                    foreach ($elementStructure->getValues() as $value) {
                        if ($value->getDsId() === $suggestNode->getDsId()) {
                            $values = array_merge($values, $value->getValue());
                        }
                    }

                    $rii = new \RecursiveIteratorIterator($elementStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
                    foreach ($rii as $structure) {
                        foreach ($structure->getValues() as $value) {
                            if ($value->getDsId() === $suggestNode->getDsId()) {
                                $values = array_merge($values, $value->getValue());
                            }
                        }
                    }
                }
            }
        }

        $values = array_unique($values);

        foreach ($values as $index => $value) {
            if (!trim($value)) {
                unset($values[$index]);
            }
        }

        return $values;
    }
}
