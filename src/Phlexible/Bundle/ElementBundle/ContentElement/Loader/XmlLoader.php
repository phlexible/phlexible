<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ContentElement\Loader;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElement;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersionMappedField;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;

/**
 * XML loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlLoader
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
     * {@inheritdoc}
     */
    public function load($eid, $version, $language)
    {
        $pathname = $this->xmlDir . '/' . $eid . '_' . $language . '.xml';

        if (!file_exists($pathname)) {
            return null;
        }

        $xml = simplexml_load_file($pathname);

        $eid = (int) $xml->eid;
        $uniqueId = (string) $xml->uniqueId;
        $elementtypeId = (int) $xml->elementtypeId;
        $elementtypeUniqueId = (string) $xml->elementtypeUniqueId;
        $elementtypeType = (string) $xml->elementtypeType;
        $elementtypeTemplate = (string) $xml->elementtypeTemplate;
        $version = (int) $xml->version;
        $language = (string) $xml->language;

        $mappedFields = array();
        foreach ($xml->mappedFields->children() as $mappedFieldNode) {
            $name = $mappedFieldNode->attributes()['name'];
            $value = (string) $mappedFieldNode;
            $mappedFields[$name] = $value;
        }
        $mappedFields = new ElementVersionMappedField($mappedFields);

        $structure = $this->loadStructure($xml->structure);

        $contentElement = new ContentElement(
            $eid,
            $uniqueId,
            $elementtypeId,
            $elementtypeUniqueId,
            $elementtypeType,
            $elementtypeTemplate,
            $version,
            $language,
            $mappedFields,
            $structure
        );

        return $contentElement;
    }

    /**
     * @param \SimpleXMLElement $structureNode
     *
     * @return ElementStructure
     */
    private function loadStructure(\SimpleXMLElement $structureNode)
    {
        $structure = new ElementStructure();

        $attr = $structureNode->attributes();

        $structure
            ->setId((int) $attr['id'])
            ->setName((string) $attr['name'])
            ->setParentName((string) $attr['parentName'])
            ->setDsId((string) $attr['dsId']);

        foreach ($structureNode->structure as $subStructureNode) {
            $structure->addStructure($this->loadStructure($subStructureNode));
        }

        foreach ($structureNode->value as $valueNode) {
            $attr = $valueNode->attributes();
            $value = (string) $valueNode;
            if ((string) $attr['dataType'] === 'array') {
                $value = json_decode($value, true);
            }

            $structure->setValue(
                new ElementStructureValue(
                    (string) $attr['id'],
                    (string) $attr['dsId'],
                    (string) $attr['language'],
                    (string) $attr['type'],
                    (string) $attr['dataType'],
                    (string) $attr['name'],
                    $value
                )
            );
        }

        return $structure;
    }
}