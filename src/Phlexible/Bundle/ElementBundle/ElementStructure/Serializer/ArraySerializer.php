<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
    public function serialize(ElementStructure $elementStructure, $language, $masterLanguage)
    {
        return $this->walk($elementStructure, $language, $masterLanguage);
    }

    /**
     * @param ElementStructure $elementStructure
     * @param string           $language
     * @param string           $masterLanguage
     *
     * @return array
     */
    private function walk(ElementStructure $elementStructure, $language, $masterLanguage)
    {
        $valueDatas = [];
        foreach ($elementStructure->getValues($language) as $value) {
            $valueData = [
                'id'         => $value->getId(),
                'dsId'       => $value->getDsId(),
                'name'       => $value->getName(),
                'type'       => $value->getType(),
                'content'    => $value->getValue(),
                'attributes' => $value->getAttributes(),
                'options'    => $value->getOptions(),
            ];
            if ($language !== $masterLanguage) {
                $masterValue = $elementStructure->getValue($value->getName(), $masterLanguage);
                if ($masterValue) {
                    $valueData['masterContent'] = $masterValue->getValue();
                }
            }
            $valueDatas[] = $valueData;
        }

        $structureDatas = [];
        foreach ($elementStructure->getStructures() as $subStructure) {
            $structureDatas[] = $this->walk($subStructure, $language, $masterLanguage);
        }

        $structureData = [
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
        ];

        return $structureData;
    }
}
