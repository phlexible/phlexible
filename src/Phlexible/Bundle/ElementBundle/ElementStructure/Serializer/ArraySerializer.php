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
    public function serialize(ElementStructure $elementStructure, $language)
    {
        return $this->walk($elementStructure, $language);
    }

    /**
     * @param ElementStructure $elementStructure
     * @param string           $language
     *
     * @return array
     */
    private function walk(ElementStructure $elementStructure, $language)
    {
        $valueDatas = array();
        foreach ($elementStructure->getValues($language) as $value) {
            $valueDatas[] = array(
                'id'         => $value->getId(),
                'dsId'       => $value->getDsId(),
                'name'       => $value->getName(),
                'type'       => $value->getType(),
                'content'    => $value->getValue(),
                'attributes' => $value->getAttributes(),
            );
        }

        $structureDatas = array();
        foreach ($elementStructure->getStructures() as $subStructure) {
            $structureDatas[] = $this->walk($subStructure, $language);
        }

        $structureData = array(
            //'id'         => $elementStructure->getId(),
            //'dataId'     => $elementStructure->getDataId(),
            'id'         => $elementStructure->getDataId(),
            'dsId'       => $elementStructure->getDsId(),
            'parentId'   => $elementStructure->getParentId(),
            'parentDsId' => $elementStructure->getParentDsId(),
            'name'       => $elementStructure->getName(),
            'parentName' => $elementStructure->getParentName(),
            'attributes' => $elementStructure->getAttributes(),
            'structures' => $structureDatas,
            'values'     => $valueDatas,
        );

        return $structureData;
    }
}