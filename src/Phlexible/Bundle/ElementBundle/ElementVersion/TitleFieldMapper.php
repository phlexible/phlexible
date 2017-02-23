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
 * Title field mapper.
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class TitleFieldMapper implements FieldMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function accept($key)
    {
        return in_array($key, ['backend', 'page', 'navigation', 'custom1', 'custom2', 'custom3', 'custom4', 'custom5']);
    }

    /**
     * {@inheritdoc}
     */
    public function map(ElementStructure $elementStructure, $language, array $mapping)
    {
        $pattern = $mapping['pattern'];
        $replace = [];
        foreach ($mapping['fields'] as $field) {
            $dsId = $field['dsId'];
            if (!$dsId) {
                continue;
            }
            $value = $this->findValue($elementStructure, $dsId, $language);
            if (!$value) {
                return null;
                //throw new \Exception("Value for dsId '$dsId' not found.");
            }
            $replace['$'.$field['index']] = $value->getValue();
        }
        $title = str_replace(array_keys($replace), array_values($replace), $pattern);

        if (!$title) {
            return null;
        }

        return $title;
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
