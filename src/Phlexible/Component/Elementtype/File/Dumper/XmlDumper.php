<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\File\Dumper;

use FluentDOM\Document;
use FluentDOM\Element;
use Phlexible\Component\Elementtype\Exception\InvalidArgumentException;
use Phlexible\Component\Elementtype\Model\Elementtype;
use Phlexible\Component\Elementtype\Model\ElementtypeStructure;
use Phlexible\Component\Elementtype\Model\ElementtypeStructureNode;

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
            [
                'id'           => $elementtype->getId(),
                'uniqueId'     => $elementtype->getUniqueId(),
                'revision'     => $elementtype->getRevision(),
                'type'         => $elementtype->getType(),
                'icon'         => $elementtype->getIcon(),
                'defaultTab'   => $elementtype->getDefaultTab(),
                'hideChildren' => $elementtype->getHideChildren() ? '1' : '0',
                'deleted'      => $elementtype->getDeleted() ? '1' : '0',
            ]
        );

        $titlesElement = $rootElement->appendElement('titles');
        foreach ($elementtype->getTitles() as $language => $title) {
            $titlesElement->appendElement('title', $title, ['language' => $language]);
        }

        $rootElement->appendElement('template', $elementtype->getTemplate());
        $rootElement->appendElement('metasetId', $elementtype->getMetaSetId());
        $rootElement->appendElement('defaultContentTab', $elementtype->getDefaultContentTab());
        $rootElement->appendElement('comment', $elementtype->getComment());
        $rootElement->appendElement('createdAt', $elementtype->getCreatedAt()->format('Y-m-d H:i:s'));
        $rootElement->appendElement('createUser', $elementtype->getCreateUser());
        $rootElement->appendElement('modifiedAt', $elementtype->getModifiedAt()->format('Y-m-d H:i:s'));
        $rootElement->appendElement('modifyUser', $elementtype->getModifyUser());

        if ($elementtype->getMappings()) {
            $mappingsElement = $rootElement->appendElement('mappings');
            foreach ($elementtype->getMappings() as $key => $mapping) {
                $mappingElement = $mappingsElement->appendElement('mapping', '', ['key' => $key, 'pattern' => $mapping['pattern']]);
                $fieldsElement = $mappingElement->appendElement('fields');
                foreach ($mapping['fields'] as $field) {
                    $fieldsElement->appendElement('field', '', $field);
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
     * @param ElementtypeStructureNode $node
     * @param Element                  $element
     *
     * @return Element
     */
    private function appendNode(ElementtypeStructure $structure, ElementtypeStructureNode $node, Element $element)
    {
        $nodeAttributes = [
            'type' => $node->getType(),
            'dsId' => $node->getDsId(),
            'name' => $node->getName(),
        ];
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
                            [
                                'type'     => $key,
                                'language' => $language
                            ]
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
                    $attributes = [
                        'key' => $key,
                        'type' => gettype($value),
                    ];
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
                        [
                            'key' => $key,
                        ]
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
