<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion;

use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;

/**
 * Custom date field mapper.
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class CustomDateFieldMapper implements FieldMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function accept($key)
    {
        return in_array($key, ['date', 'time', 'datetime']);
    }

    /**
     * {@inheritdoc}
     */
    public function map(ElementStructure $elementStructure, $language, array $mapping)
    {
        $mappings = [];
        foreach ($mapping['fields'] as $field) {
            $dsId = $field['dsId'];
            $mappings[$field['type']] = $this->findValue($elementStructure, $dsId, $language);
        }
        $replace = [];
        if (isset($mappings['datetime'])) {
            $replace[] = $mappings['datetime']->getValue();
        }
        if (isset($mappings['date'])) {
            $replace[] = $mappings['date']->getValue();
        }
        if (isset($mappings['time'])) {
            $replace[] = $mappings['time']->getValue();
        }
        if (!count($replace)) {
            return null;
        }

        return implode(' ', $replace);
    }

    /**
     * @param ElementStructure $elementStructure
     * @param string           $dsId
     * @param string           $language
     *
     * @return null|ElementStructureValue
     */
    private function findValue(ElementStructure $elementStructure, $dsId, $language)
    {
        if ($elementStructure->hasValueByDsId($dsId, $language)) {
            return $elementStructure->getValueByDsId($dsId, $language);
        }

        foreach ($elementStructure->getStructures() as $childStructure) {
            $value = $this->findValue($childStructure, $dsId, $language);
            if ($value) {
                return $value;
            }
        }

        return null;
    }
}
