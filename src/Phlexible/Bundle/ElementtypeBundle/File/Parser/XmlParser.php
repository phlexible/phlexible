<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\File\Parser;

use Phlexible\Bundle\ElementtypeBundle\File\Loader\LoaderInterface;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;

/**
 * XML parser
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlParser implements ParserInterface
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(\SimpleXMLElement $xml)
    {
        $rootAttr = $xml->attributes();
        $id = (string) $rootAttr['id'];
        $uniqueId = (string) $rootAttr['uniqueId'];
        $revision = (int) $rootAttr['revision'];
        $type = (string) $rootAttr['type'];
        $icon = (string) $rootAttr['icon'];
        $defaultTab = (int) $rootAttr['defaultTab'];

        $comment = (string) $xml->comment;
        $metasetId = (string) $xml->metaset;
        $defaultContentTab = (int) $xml->defaultContentTab;
        $createdAt = (string) $xml->createdAt;
        $createUserId = (string) $xml->createUserId;
        $modifiedAt = (string) $xml->modifiedAt;
        $modifyUserId = (string) $xml->modifyUserId;

        $mappings = array();
        if ($xml->mappings) {
            foreach ($xml->mappings->mapping as $mapping) {
                $mappingAttr = $mapping->attributes();
                $key = (string) $mappingAttr['key'];
                $pattern = (string) $mapping->pattern;
                $fields = array();
                foreach ($mapping->fields->field as $field) {
                    $fields[] = array(
                        'dsId'  => (string) $field->dsId,
                        'title' => (string) $field->title,
                        'index' => (int) $field->index,
                    );
                }
                $mappings[$key] = array(
                    'pattern' => $pattern,
                    'fields'  => $fields,
                );
            }
        }

        $titles = array();
        if ($xml->titles) {
            foreach ($xml->titles->title as $title) {
                $titleAttr = $title->attributes();
                $language = (string) $title['language'];
                $titles[$language] = (string) $title;
            }
        }

        $elementtypeStructure = $this->loadStructure($xml->structure);

        $elementtype = new Elementtype();
        $elementtype
            ->setId($id)
            ->setUniqueId($uniqueId)
            ->setType($type)
            ->setTitles($titles)
            ->setIcon($icon)
            ->setComment($comment)
            ->setMetaSetId($metasetId)
            ->setMappings($mappings)
            ->setRevision($revision)
            ->setDefaultTab($defaultTab)
            ->setDefaultContentTab($defaultContentTab)
            ->setStructure($elementtypeStructure)
            ->setCreatedAt(new \DateTime($createdAt))
            ->setCreateUserId($createUserId)
            ->setModifiedAt(new \DateTime($modifiedAt))
            ->setModifyUserId($modifyUserId)
        ;

        return $elementtype;
    }

    /**
     * @param \SimpleXMLElement $structure
     *
     * @return ElementtypeStructure
     */
    private function loadStructure(\SimpleXMLElement $structure)
    {
        $elementtypeStructure = new ElementtypeStructure();

        foreach ($structure->node as $node) {
            $this->loadNode($node, $elementtypeStructure);
        }

        return $elementtypeStructure;
    }

    /**
     * @param \SimpleXMLElement        $node
     * @param ElementtypeStructure     $elementtypeStructure
     * @param ElementtypeStructureNode $parentNode
     */
    private function loadNode(\SimpleXMLElement $node, ElementtypeStructure $elementtypeStructure, ElementtypeStructureNode $parentNode = null, $isReferenced = false)
    {
        $nodeAttr = $node->attributes();
        $type = (string) $nodeAttr['type'];
        $dsId = (string) $nodeAttr['dsId'];
        $name = (string) $nodeAttr['name'];
        $referenceElementtypeId = (string) $nodeAttr['referenceElementtypeId'] ? (string) $nodeAttr['referenceElementtypeId'] : null;

        $labels = array();
        if ($node->labels) {
            foreach ($node->labels->label as $label) {
                $itemAttr = $label->attributes();
                $labelType = (string) $itemAttr['type'];
                $language = (string) $itemAttr['language'];
                $labels[$labelType][$language] = (string) $label;
            }
        }

        $configuration = array();
        if ($node->configuration) {
            foreach ($node->configuration->item as $item) {
                $itemAttr = $item->attributes();
                $key = (string) $itemAttr['key'];
                $configType = (string) $itemAttr['type'];
                $configuration[$key] = (string) $item;
                if ($configType === 'json_array') {
                    $configuration[$key] = json_decode($configuration[$key], true);
                } elseif ($configType === 'boolean') {
                    $configuration[$key] = (bool) $configuration[$key];
                } elseif ($configType === 'integer') {
                    $configuration[$key] = (int) $configuration[$key];
                } elseif ($configType === 'float') {
                    $configuration[$key] = (float) $configuration[$key];
                } elseif ($configType === 'double') {
                    $configuration[$key] = (float) $configuration[$key];
                }
            }
        }

        $options = array();
        if ($node->options) {
            foreach ($node->options->option as $option) {
                $itemAttr = $option->attributes();
                $key = (string) $itemAttr['key'];
                foreach ($option->value as $optionValue) {
                    $optionValueAttr = $optionValue->attributes();
                    $language = (string) $optionValueAttr['language'];
                    $options[$key][$language] = (string) $optionValue;
                }
            }
        }

        $validation = array();
        if ($node->validation) {
            foreach ($node->validation->constrain as $constrain) {
                $itemAttr = $constrain->attributes();
                $key = (string) $itemAttr['key'];
                $validation[$key] = (string) $constrain;
            }
        }

        $comment = (string) $node->comment;

        $elementtypeStructureNode = new ElementtypeStructureNode();
        $elementtypeStructureNode
            ->setType($type)
            ->setDsId($dsId)
            ->setParentNode($parentNode)
            ->setName($name)
            ->setComment($comment)
            ->setConfiguration($configuration)
            ->setValidation($validation)
            ->setLabels($labels)
            ->setReferenceElementtypeId($referenceElementtypeId)
            ->setReferenced($isReferenced);

        $elementtypeStructure->addNode($elementtypeStructureNode);

        if ($node->children) {
            foreach ($node->children->node as $childNode) {
                $this->loadNode($childNode, $elementtypeStructure, $elementtypeStructureNode, $isReferenced);
            }
        }

        if ($referenceElementtypeId) {
            $referenceXml = $this->loader->open($referenceElementtypeId);
            foreach ($referenceXml->structure->node as $referenceNode) {
                $this->loadNode($referenceNode, $elementtypeStructure, $elementtypeStructureNode, true);
            }
        }
    }
}
