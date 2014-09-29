<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Distiller;

use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\ElementtypeBundle\Field\FieldRegistry;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;

/**
 * Distiller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Distiller
{
    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @var FieldRegistry
     */
    private $fieldRegistry;

    /**
     * @param ElementtypeService $elementtypeService
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
    public function distill(ElementtypeVersion $elementtypeVersion)
    {
        $elementtypeStructure = $this->elementtypeService->findElementtypeStructure($elementtypeVersion);

        $rootNode = $elementtypeStructure->getRootNode();
        $data = $this->iterate($elementtypeStructure, $rootNode);

        return $data;
    }

    private function iterate(ElementtypeStructure $structure, ElementtypeStructureNode $node)
    {
        $data = array();

        foreach ($structure->getChildNodes($node->getDsId()) as $childNode) {
            $field = $this->fieldRegistry->getField($childNode->getFieldType());

            if ($field->isField()) {
                $data[$childNode->getWorkingTitle()] = array(
                    'node'  => $childNode,
                    'field' => $field,
                );
            }

            if ($structure->hasChildNodes($childNode->getDsId())) {
                $childData = $this->iterate($structure, $childNode);

                if ($childNode->isRepeatable() || $childNode->isOptional()) {
                    $data[$node->getWorkingTitle()] = array(
                        'node'     => $childNode,
                        'field'    => $field,
                        'children' => $childData
                    );
                } else {
                    $data = array_merge($data, $childData);
                }
            }
        }

        return $data;
    }
}
