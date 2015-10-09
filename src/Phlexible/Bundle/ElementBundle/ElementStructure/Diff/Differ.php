<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementStructure\Diff;

use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;

/**
 * Differ
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Differ
{
    /**
     * @param ElementStructure $structure
     * @param ElementStructure $compareStructure
     */
    public function diff(ElementStructure $structure, ElementStructure $compareStructure)
    {
        foreach ($structure->getValues() as $structureValue) {
            $name = $structureValue->getName();
            $fromValue = $structureValue->getValue();
            if ($compareStructure && $compareStructure->hasValue($name, $structureValue->getLanguage())) {
                $compareStructureValue = $compareStructure->getValue($name, $structureValue->getLanguage());
                $toValue = $compareStructureValue->getValue();

                if ($fromValue !== $toValue) {
                    $this->applyModifiedValue($structureValue, $toValue);
                }
            } else {
                $this->applyAddedValue($structureValue);
            }
        }

        foreach ($compareStructure->getValues() as $compareStructureValue) {
            if (!$structure->hasValue($compareStructureValue->getName(), $compareStructureValue->getLanguage())) {
                $this->applyRemovedValue($compareStructureValue);
                $structure->setValue($compareStructureValue);
            }
        }

        foreach ($structure->getStructures() as $structureChild) {
            $found = false;
            foreach ($compareStructure->getStructures() as $compareStructureChild) {
                if ($structureChild->getId() === $compareStructureChild->getId()) {
                    $found = true;
                    $this->diff($structureChild, $compareStructureChild);
                    break;
                }
            }
            if ($found) {
                $this->applyAdded($structureChild);
            }
        }

        foreach ($compareStructure->getStructures() as $compareStructureChild) {
            $found = false;
            foreach ($structure->getStructures() as $structureChild) {
                if ($structureChild->getId() === $compareStructureChild->getId()) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $this->applyRemoved($compareStructureChild);
                $structure->addStructure($compareStructureChild);
            }
        }
    }

    /**
     * @param ElementStructure $structure
     */
    private function applyAdded(ElementStructure $structure)
    {
        $structure
            ->setAttribute('diff', 'added');

        foreach ($structure->getValues() as $value) {
            //$this->applyAddedValue($value);
        }

        foreach ($structure->getStructures() as $childStructure) {
            $this->applyAdded($structure);
        }
    }

    /**
     * @param ElementStructureValue $value
     */
    private function applyAddedValue(ElementStructureValue $value)
    {
        $value
            ->setAttribute('diff', 'added')
            ->setAttribute('oldValue', '');
    }

    /**
     * @param ElementStructureValue $value
     * @param mixed                 $oldValue
     */
    private function applyModifiedValue(ElementStructureValue $value, $oldValue)
    {
        $granularity = new \cogpowered\FineDiff\Granularity\Word;
        $diff = new \cogpowered\FineDiff\Diff($granularity);

        $value
            ->setAttribute('diff', 'modified')
            ->setAttribute('oldValue', $oldValue)
            ->setAttribute('diffValue', $diff->render($oldValue, $value->getValue()));
    }

    /**
     * @param ElementStructure $structure
     */
    private function applyRemoved(ElementStructure $structure)
    {
        $structure
            ->setAttribute('diff', 'removed');

        foreach ($structure->getValues() as $value) {
            //$this->applyRemovedValue($value);
        }

        foreach ($structure->getStructures() as $childStructure) {
            $this->applyRemoved($childStructure);
        }
    }

    /**
     * @param ElementStructureValue $value
     */
    private function applyRemovedValue(ElementStructureValue $value)
    {
        $value
            ->setAttribute('diff', 'removed')
            ->setAttribute('oldValue', $value->getValue())
            //->setValue('')
        ;
    }
}
