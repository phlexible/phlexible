<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion;

use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;

/**
 * Custom date field mapper
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
        return in_array($key, array('customDate'));
    }

    /**
     * {@inheritdoc}
     */
    public function map(ElementStructure $elementStructure, array $mapping)
    {
        $mappings = array();
        foreach ($mapping['fields'] as $field) {
            $dsId = $field['ds_id'];
            $mappings[$field['type']] = $this->findValue($elementStructure, $dsId);
        }
        $replace = array();
        if (isset($mappings['datetime'])) {
            $replace[] = $mappings['datetime'];
        }
        if (isset($mappings['date'])) {
            $replace[] = $mappings['date'];
        }
        if (isset($mappings['time'])) {
            $replace[] = $mappings['time'];
        }
        if (!count($replace)) {
            return null;
        }

        return implode(' ', $replace);
    }

    /**
     * @param ElementStructure $elementStructure
     * @param string           $dsId
     *
     * @return null|ElementStructureValue
     */
    private function findValue(ElementStructure $elementStructure, $dsId)
    {
        if ($elementStructure->hasValueByDsId($dsId)) {
            return $elementStructure->getValueByDsId($dsId);
        }

        foreach ($elementStructure->getStructures() as $childStructure) {
            $value = $this->findValue($childStructure, $dsId);
            if ($value) {
                return $value;
            }
        }

        return null;
    }
}