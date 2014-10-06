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
use Phlexible\Bundle\ElementtypeBundle\Exception\InvalidArgumentException;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;

/**
 * XML dumper
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
                    $attributes = array(
                        'dsId'  => $field['dsId'],
                        'title' => $field['title'],
                        'index' => $field['index'],
                    );
                    $fieldsElement->appendElement('field', '', $attributes);
                }
            }
        }

        $structureElement = $rootElement->appendElement('structure');
        $structure = $elementtype->getStructure();

        if ($structure) {
            $rootNode = $structure->getRootNode();
            if ($rootNode) {
                $this->appendNode($structure, $rootNode, $structureElement);
            }
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

        $labels = $node->getLabels();
        if ($labels) {
            foreach ($labels as $key => $value) {
                foreach ($value as $language => $languageValue) {
                    if (!$languageValue) {
                        unset($labels[$key][$language]);
                    }
                }
            }
            if ($labels) {
                $labelsElement = $nodeElement->appendElement('labels');
                foreach ($labels as $key => $value) {
                    foreach ($value as $language => $languageValue) {
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
        }

        $configuration = $node->getConfiguration();
        if ($configuration) {
            foreach ($configuration as $key => $value) {
                if (!$value) {
                    unset($configuration[$key]);
                }
            }
            if ($configuration) {
                $configurationElement = $nodeElement->appendElement('configuration');
                foreach ($configuration as $key => $value) {
                    $attributes = array(
                        'key' => $key,
                        'type' => gettype($value),
                    );
                    if (is_array($value)) {
                        $value = json_encode($value);
                        $attributes['type'] = 'json_array';
                    } elseif (!is_scalar($value)) {
                        throw new InvalidArgumentException('Value has to be array or scalar.');
                    }
                    $configurationElement->appendElement(
                        'item',
                        $value,
                        $attributes
                    );
                }
            }
        }

        $validation = $node->getValidation();
        if ($validation) {
            foreach ($validation as $key => $value) {
                if (!$value) {
                    unset($validation[$key]);
                }
            }
            if ($validation) {
                $validationElement = $nodeElement->appendElement('validation');
                foreach ($validation as $key => $value) {
                    $validationElement->appendElement(
                        'constraint',
                        $value,
                        array(
                            'key' => $key,
                        )
                    );
                }
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
