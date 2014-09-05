<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementStructure\Serializer;

use Phlexible\Bundle\ElementBundle\Model\ElementStructure;

/**
 * Serializer interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ArraySerializer implements SerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serialize(ElementStructure $elementStructure)
    {
        return $this->walk($elementStructure);
    }

    private function walk(ElementStructure $elementStructure)
    {
        $valueDatas = array();
        foreach ($elementStructure->getValues() as $value) {
            $valueDatas[] = array(
                'id'      => $value->getId(),
                'dsId'    => $value->getDsId(),
                'name'    => $value->getName(),
                'type'    => $value->getType(),
                'content' => $value->getValue(),
            );
        }

        $structureDatas = array();
        foreach ($elementStructure->getStructures() as $subStructure) {
            $structureDatas[] = $this->walk($subStructure);
        }

        $structureData = array(
            'id'         => $elementStructure->getId(),
            'dsId'       => $elementStructure->getDsId(),
            'parentId'   => $elementStructure->getRepeatableId(),
            'parentDsId' => $elementStructure->getRepeatableDsId(),
            'name'       => $elementStructure->getName(),
            'parentName' => $elementStructure->getParentName(),
            'structures' => $structureDatas,
            'values'     => $valueDatas,
        );

        return $structureData;
    }
}