<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ContentElement\Loader;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElement;
use Phlexible\Bundle\ElementBundle\ElementStructure\ElementStructure;
use Phlexible\Bundle\ElementBundle\ElementStructure\ElementStructureValue;

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
     * @param string $filename
     *
     * @return ContentElement
     */
    public function load($filename)
    {
        $pathname = $this->xmlDir . '/' . $filename;

        if (!file_exists($pathname)) {
            return null;
        }

        $xml = simplexml_load_file($pathname);

        $eid = (int) $xml->eid;
        $uniqueId = (string) $xml->uniqueId;
        $elementtypeId = (int) $xml->elementtypeId;
        $elementtypeUniqueId = (string) $xml->elementtypeUniqueId;
        $elementtypeType = (string) '';
        $version = (int) $xml->version;
        $language = (string) $xml->language;

        $mappedFields = array();
        foreach ($xml->mappedFields->children() as $mappedFieldNode) {
            $name = $mappedFieldNode->attributes()['name'];
            $value = (string) $mappedFieldNode;
            $mappedFields[$name] = $value;
        }

        $structure = $this->loadStructure($xml->structure);

        $contentElement = new ContentElement(
            $eid,
            $uniqueId,
            $elementtypeId,
            $elementtypeUniqueId,
            $elementtypeType,
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
    public function loadStructure(\SimpleXMLElement $structureNode)
    {
        $structure = new ElementStructure();

        $attr = $structureNode->attributes();

        $structure
            ->setId((int) $attr['id'])
            ->setName((string) $attr['name'])
            ->setParentName((string) $attr['parentName'])
            ->setDsId((string) $attr['dsId'])
            ->setParentDsId((string) $attr['parentDsId'])
        ;

        foreach ($structureNode->structure as $subStructureNode) {
            $structure->addStructure($this->loadStructure($subStructureNode));
        }

        foreach ($structureNode->value as $valueNode) {
            $attr = $valueNode->attributes();
            $structure->setValue(
                new ElementStructureValue(
                    (string) $attr['dsId'],
                    (string) $attr['name'],
                    (string) $attr['type'],
                    (string) $valueNode
                )
            );
        }

        return $structure;
    }
}