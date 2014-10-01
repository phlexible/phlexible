<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\File\Dumper;

use FluentDOM\Document;
use FluentDOM\Element;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;

/**
 * XML loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlDumper implements DumperInterface
{
    /**
     * {@inheritdoc}
     */
    public function dump(Elementtype $elementtype)
    {
        $dom = new Document();
        $dom->formatOutput = true;

        $rootElement = $dom->appendElement(
            'elementtype',
            '',
            array(
                'id'         => $elementtype->getId(),
                'uniqueId'   => $elementtype->getUniqueId(),
                'revision'   => $elementtype->getRevision(),
                'type'       => $elementtype->getType(),
                'icon'       => $elementtype->getIcon(),
                'defaultTab' => $elementtype->getDefaultTab(),
            )
        );

        $titlesElement = $rootElement->appendElement('titles');
        foreach ($elementtype->getTitles() as $language => $title) {
            $titlesElement->appendElement('title', $title, array('language' => $language));
        }

        $rootElement->appendElement('comment', $elementtype->getComment());
        $rootElement->appendElement('metasetId', $elementtype->getMetaSetId());
        $rootElement->appendElement('defaultContentTab', $elementtype->getDefaultContentTab());
        $rootElement->appendElement('createdAt', $elementtype->getCreatedAt()->format('Y-m-d H:i:s'));
        $rootElement->appendElement('createUserId', $elementtype->getCreateUserId());
        $rootElement->appendElement('modifiedAt', $elementtype->getModifiedAt()->format('Y-m-d H:i:s'));
        $rootElement->appendElement('modifyUserId', $elementtype->getModifyUserId());

        if ($elementtype->getMappings()) {
            $mappingsElement = $rootElement->appendElement('mappings');
            foreach ($elementtype->getMappings() as $key => $mapping) {
                $mappingElement = $mappingsElement->appendElement('mapping', '', array('key' => $key));
                $mappingElement->appendElement('pattern', $mapping['pattern']);
                $fieldsElement = $mappingElement->appendElement('fields');
                foreach ($mapping['fields'] as $field) {
                    $fieldElement = $fieldsElement->appendElement('field');
                    $fieldElement->appendElement('dsId', $field['ds_id']);
                    $fieldElement->appendElement('title', $field['field']);
                    $fieldElement->appendElement('index', $field['index']);
                }
            }
        }

        $structureElement = $rootElement->appendElement('structure');
        $structure = $elementtype->getStructure();

        $rootNode = $structure->getRootNode();
        if ($rootNode) {
            $this->appendNode($structure, $rootNode, $structureElement);
        }

        return $dom->saveXML();

    }

    /**
     * @param ElementtypeStructure     $structure
     * @param \Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode $node
     * @param Element                  $element
     *
     * @return Element
     */
    private function appendNode(ElementtypeStructure $structure, ElementtypeStructureNode $node, Element $element)
    {
        $nodeAttributes = array(
            'type' => $node->getType(),
            'dsId' => $node->getDsId(),
            'name' => $node->getName(),
        );
        if ($node->getReferenceElementtypeId()) {
            $nodeAttributes['referenceElementtypeId'] = $node->getReferenceElementtypeId();
        }

        $nodeElement = $element->appendElement('node', '', $nodeAttributes);

        if ($node->getLabels()) {
            $labelsElement = $nodeElement->appendElement('labels');
            foreach ($node->getLabels() as $key => $value) {
                foreach ($value as $language => $languageValue) {
                    if (!$languageValue) {
                        continue;
                    }
                    $labelsElement->appendElement(
                        'label',
                        $languageValue,
                        array(
                            'type'     => $key,
                            'language' => $language
                        )
                    );
                }
            }
        }

        if ($node->getConfiguration()) {
            $configurationElement = $nodeElement->appendElement('configuration');
            foreach ($node->getConfiguration() as $key => $value) {
                if (!$value) {
                    continue;
                }
                $attributes = array(
                    'key' => $key,
                    'type' => gettype($value),
                );
                if (is_array($value)) {
                    $value = json_encode($value);
                    $attributes['type'] = 'json_array';
                } elseif (!is_scalar($value)) {
                    throw new \Exception('Value has to be array or scalar.');
                }
                $configurationElement->appendElement(
                    'item',
                    $value,
                    $attributes
                );
            }
        }

        if ($node->getValidation()) {
            $validationElement = $nodeElement->appendElement('validation');
            foreach ($node->getValidation() as $key => $value) {
                if (!$value) {
                    continue;
                }
                $validationElement->appendElement(
                    'constrain',
                    $value,
                    array(
                        'key' => $key,
                    )
                );
            }
        }

        $childNodes = $structure->getChildNodes($node->getDsId());
        if ($childNodes) {
            $childrenElement = $nodeElement->appendElement('children');
            foreach ($childNodes as $childNode) {
                $this->appendNode($structure, $childNode, $childrenElement);
            }
        }

        return $nodeElement;
    }
}
