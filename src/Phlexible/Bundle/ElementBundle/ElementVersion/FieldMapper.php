<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;

/**
 * Field mapper
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class FieldMapper
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @param ElementService $elementService
     */
    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
    }

    /**
     * @param ElementVersion $elementVersion
     * @param string         $language
     *
     * @return array
     */
    public function map(ElementVersion $elementVersion, $language)
    {
        $elementtypeVersion = $this->elementService->findElementtypeVersion($elementVersion);
        $mappings = $elementtypeVersion->getMappings();
        $elementStructure = $this->elementService->findElementStructure($elementVersion, $language);

        $titles = array();
        foreach ($mappings as $key => $mapping) {
            if (in_array($key, array('backend', 'page', 'navigation'))) {
                $pattern = $mapping['pattern'];
                $replace = array();
                foreach ($mapping['fields'] as $field) {
                    $dsId = $field['ds_id'];
                    $replace['$' . $field['index']] = $this->findValue($elementStructure, $dsId);
                }
                $title = str_replace(array_keys($replace), array_values($replace), $pattern);
                if ($title) {
                    $titles[$key] = $title;
                }
            } elseif ($key === 'customDate') {
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
                if (count($replace)) {
                    $titles[$key] = implode(' ', $replace);
                }
            } else {

            }
        }

        if (empty($titles['backend'])) {
            $elementtype = $elementtypeVersion->getElementtype();
            $titles['backend'] = '[' . $elementtype->getTitle() . ' ' . $language . ']';
        }

        return $titles;
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