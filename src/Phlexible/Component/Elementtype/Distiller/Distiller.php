<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\Distiller;

use Phlexible\Component\Elementtype\ElementtypeService;
use Phlexible\Component\Elementtype\Field\FieldRegistry;
use Phlexible\Component\Elementtype\Model\Elementtype;
use Phlexible\Component\Elementtype\Model\ElementtypeStructure;
use Phlexible\Component\Elementtype\Model\ElementtypeStructureNode;

/**
 * Distiller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Distiller
{
    /**
     * @var \Phlexible\Component\Elementtype\ElementtypeService
     */
    private $elementtypeService;

    /**
     * @var FieldRegistry
     */
    private $fieldRegistry;

    /**
     * @param \Phlexible\Component\Elementtype\ElementtypeService $elementtypeService
     * @param FieldRegistry      $fieldRegistry
     */
    public function __construct(ElementtypeService $elementtypeService, FieldRegistry $fieldRegistry)
    {
        $this->elementtypeService = $elementtypeService;
        $this->fieldRegistry = $fieldRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function distill(Elementtype $elementtype)
    {
        $elementtypeStructure = $elementtype->getStructure();

        $rootNode = $elementtypeStructure->getRootNode();
        $data = $this->iterate($elementtypeStructure, $rootNode);

        return $data;
    }

    private function iterate(ElementtypeStructure $structure, ElementtypeStructureNode $node)
    {
        $data = [];

        foreach ($structure->getChildNodes($node->getDsId()) as $childNode) {
            $field = $this->fieldRegistry->getField($childNode->getType());

            if ($field->isField()) {
                $data[$childNode->getName()] = [
                    'node'  => $childNode,
                    'field' => $field,
                ];
            }

            if ($structure->hasChildNodes($childNode->getDsId())) {
                $childData = $this->iterate($structure, $childNode);

                if ($childNode->isRepeatable() || $childNode->isOptional()) {
                    $data[$node->getName()] = [
                        'node'     => $childNode,
                        'field'    => $field,
                        'children' => $childData
                    ];
                } else {
                    $data = array_merge($data, $childData);
                }
            }
        }

        return $data;
    }
}
