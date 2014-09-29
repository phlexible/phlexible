<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\File\Loader;

use FluentDOM\Document;
use FluentDOM\Element;
use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeStructureNode;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;

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
                'name'       => $elementtype->getName(),
                'revision'   => $elementtype->getRevision(),
                'type'       => $elementtype->getType(),
                'icon'       => $elementtype->getIcon(),
                'defaultTab' => $elementtype->getDefaultTab(),
            )
        );

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
     * @param ElementtypeStructureNode $node
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
                    if ($key === 'context_help') {
                        $key = 'contextHelp';
                    }
                    if ($key === 'fieldlabel') {
                        $key = 'fieldLabel';
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
                $configurationElement->appendElement(
                    'item',
                    $value,
                    array('key' => $key)
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

        if ($node->getOptions()) {
            $optionsElement = $nodeElement->appendElement('options');
            foreach ($node->getOptions() as $option) {
                $optionsElement->appendElement(
                    'option',
                    '',
                    array(
                        'key' => $option['key'],
                        'de'  => $option['de'],
                        'en'  => $option['en'],
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
