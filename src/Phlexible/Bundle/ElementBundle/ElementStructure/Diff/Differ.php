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
 * Differ
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Differ
{
    /**
     * @param ElementStructure $fromStructure
     * @param ElementStructure $toStructure
     *
     * @return Diff
     */
    public function diff(ElementStructure $fromStructure, ElementStructure $toStructure)
    {
        $diff = new Diff();

        $this->doDiff($diff, $fromStructure, $toStructure);

        return $diff;
    }

    /**
     * @param Diff             $diff
     * @param ElementStructure $fromStructure
     * @param ElementStructure $toStructure
     *
     * @return Diff
     */
    private function doDiff(Diff $diff, ElementStructure $fromStructure = null, ElementStructure $toStructure = null)
    {
        if ($fromStructure) {
            foreach ($fromStructure->getValues() as $fromElementStructureValue) {
                $name = $fromElementStructureValue->getName();
                $fromValue = $fromElementStructureValue->getValue();
                if ($toStructure && $toStructure->hasValue($name)) {
                    $toElementStructureValue = $toStructure->getValue($name);
                    $toValue = $toElementStructureValue->getValue();

                    if ($fromValue !== $toValue) {
                        $diff->addModified($fromStructure, $fromElementStructureValue, $toElementStructureValue);
                    }
                } else {
                    $diff->addRemoved($fromStructure, $fromElementStructureValue);
                }
            }
        }

        if ($toStructure) {
            foreach ($toStructure->getValues() as $toElementStructureValue) {
                if (!$fromStructure || !$fromStructure->hasValue($toElementStructureValue->getName())) {
                    $diff->addAdded($toStructure, $toElementStructureValue);
                }
            }
        }

        if ($fromStructure) {
            foreach ($fromStructure->getStructures() as $fromStructureChild) {
                if ($toStructure) {
                    foreach ($toStructure->getStructures() as $toStructureChild) {
                        if ($fromStructureChild->getId() === $toStructureChild->getId()) {
                            $this->doDiff($diff, $fromStructureChild, $toStructureChild);
                            break 2;
                        }
                    }
                }
                $this->doDiff($diff, $fromStructureChild, null);
            }
        }

        if ($toStructure) {
            foreach ($toStructure->getStructures() as $toStructureChild) {
                if ($fromStructure) {
                    foreach ($fromStructure->getStructures() as $fromStructureChild) {
                        echo $fromStructureChild->getId()." ".$toStructureChild->getId().PHP_EOL;
                        if ($fromStructureChild->getId() === $toStructureChild->getId()) {
                            break 2;
                        }
                    }
                }
                $this->doDiff($diff, null, $toStructureChild);
            }
        }

        return $diff;
    }
}