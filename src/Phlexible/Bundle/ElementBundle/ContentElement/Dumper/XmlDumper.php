<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ContentElement\Dumper;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElement;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;

/**
 * XML dumper
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlDumper implements DumperInterface
{
    /**
     * @var string
     */
    private $xmlDir;

    /**
     * @param string $xmlDir
     */
    public function __construct($xmlDir)
    {
        $this->xmlDir = $xmlDir;
    }

    /**
     * @param ContentElement $contentElement
     */
    public function dump(ContentElement $contentElement)
    {
        $filename = $contentElement->getEid() . '_' . $contentElement->getLanguage() . '.xml';

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $rootNode = $dom->createElement('contentElement');
        $dom->appendChild($rootNode);

        $rootNode->appendChild($dom->createElement('eid', $contentElement->getEid()));
        $rootNode->appendChild($dom->createElement('uniqueId', $contentElement->getUniqueId()));
        $rootNode->appendChild($dom->createElement('elementtypeId', $contentElement->getElementtypeId()));
        $rootNode->appendChild($dom->createElement('elementtypeUniqueId', $contentElement->getElementtypeUniqueId()));
        $rootNode->appendChild($dom->createElement('elementtypeType', $contentElement->getElementtypeType()));
        $rootNode->appendChild($dom->createElement('version', $contentElement->getVersion()));
        $rootNode->appendChild($dom->createElement('language', $contentElement->getLanguage()));

        $mappedFieldsNode = $dom->createElement('mappedFields');
        $rootNode->appendChild($mappedFieldsNode);
        foreach ($contentElement->getMappedField() as $key => $value) {
            $valueNode = $dom->createElement($key);
            $valueNode->appendChild($dom->createCDATASection($value));
            $mappedFieldsNode->appendChild($valueNode);
        }

        $rootNode->appendChild(
            $this->createStructureNodes($dom, $contentElement->getStructure())
        );

        if (!file_exists($this->xmlDir)) {
            mkdir($this->xmlDir, 0777, true);
        }

        $dom->save($this->xmlDir . '/' . $filename);
    }

    /**
     * @param \DOMDocument     $dom
     * @param ElementStructure $structure
     *
     * @return \DOMElement
     */
    private function createStructureNodes(\DOMDocument $dom, ElementStructure $structure)
    {
        $structureNode = $dom->createElement('structure');

        $idAttr = $dom->createAttribute('id');
        $idAttr->value = $structure->getId();
        $structureNode->appendChild($idAttr);

        $dsIdAttr = $dom->createAttribute('dsId');
        $dsIdAttr->value = $structure->getDsId();
        $structureNode->appendChild($dsIdAttr);

        $parentDsIdAttr = $dom->createAttribute('parentDsId');
        $parentDsIdAttr->value = $structure->getDsId();
        $structureNode->appendChild($parentDsIdAttr);

        $nameAttr = $dom->createAttribute('name');
        $nameAttr->value = $structure->getName();
        $structureNode->appendChild($nameAttr);

        $parentNameAttr = $dom->createAttribute('parentName');
        $parentNameAttr->value = $structure->getParentName();
        $structureNode->appendChild($parentNameAttr);

        foreach ($structure->getStructures() as $childStructure) {
            $structureNode->appendChild(
                $this->createStructureNodes($dom, $childStructure)
            );
        }

        foreach ($structure->getValues() as $value) {
            $valueNode = $dom->createElement('value');

            $idAttr = $dom->createAttribute('id');
            $idAttr->value = $value->getId();
            $valueNode->appendChild($idAttr);

            $dsIdAttr = $dom->createAttribute('dsId');
            $dsIdAttr->value = $value->getDsId();
            $valueNode->appendChild($dsIdAttr);

            $languageAttr = $dom->createAttribute('language');
            $languageAttr->value = $value->getLanguage();
            $valueNode->appendChild($languageAttr);

            $nameAttr = $dom->createAttribute('name');
            $nameAttr->value = $value->getName();
            $valueNode->appendChild($nameAttr);

            $typeAttr = $dom->createAttribute('type');
            $typeAttr->value = $value->getType();
            $valueNode->appendChild($typeAttr);

            $dataTypeAttr = $dom->createAttribute('dataType');
            $dataTypeAttr->value = $value->getDataType();
            $valueNode->appendChild($dataTypeAttr);

            $rawValue = $value->getValue();
            if ($value->getDataType() === 'array') {
                $rawValue = json_encode($rawValue);
            }
            $valueNode->appendChild($dom->createCDATASection($rawValue));

            $structureNode->appendChild($valueNode);
        }

        return $structureNode;
    }
}