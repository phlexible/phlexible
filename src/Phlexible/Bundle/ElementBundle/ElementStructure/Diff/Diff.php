<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementStructure\Diff;

use Phlexible\Bundle\ElementBundle\Model\ElementStructure;

/**
 * Diff
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Diff
{
    /**
     * @param ElementStructure $from
     * @param ElementStructure $to
     *
     * @return ElementStructure
     */
    public function diff(ElementStructure $from, ElementStructure $to)
    {
        $diff = array(
            'values' => array(
                'add' => array(),
                'mod' => array(),
                'del' => array(),
            )
        );

        foreach ($from->getValues() as $fromElementStructureValue) {
            $name = $fromElementStructureValue->getName();
            $fromValue = $fromElementStructureValue->getValue();
            if ($to->hasValue($name)) {
                $toElementStructureValue = $to->getValue($name);
                $toValue = $toElementStructureValue->getValue();

                if ($fromValue !== $toValue) {
                    $diff['values']['mod'][] = array('from' => $fromElementStructureValue, 'to' => $toElementStructureValue);
                }
            } else {
                $diff['values']['del'][] = array('from' => $fromElementStructureValue);
            }
        }

        foreach ($to->getValues() as $toElementStructureValue) {
            if (!$from->hasValue($toElementStructureValue->getName())) {
                $diff['values']['add'][] = array('to' => $toElementStructureValue);
            }
        }

        foreach ($from->getStructures() as $fromStructure) {
            foreach ($to->getStructures() as $toStructure) {
                if ($fromStructure->getId() === $toStructure->getId()) {
                    $diff['structures']['mod'][] = $this->diff($fromStructure, $toStructure);
                    break 2;
                }
            }
            $diff['structures']['del'][] = $fromStructure;
        }

        foreach ($to->getStructures() as $toStructure) {
            foreach ($from->getStructures() as $fromStructure) {
                if ($fromStructure->getId() === $toStructure->getId()) {
                    $diff['structures']['mod'][] = $this->diff($fromStructure, $toStructure);
                    break 2;
                }
            }
            $diff['structures']['add'][] = $toStructure;
        }

        return $diff;
    }
}