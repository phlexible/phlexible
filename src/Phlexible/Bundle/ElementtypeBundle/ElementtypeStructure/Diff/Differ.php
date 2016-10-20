<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\Diff;

use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;

/**
 * Differ
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Differ
{
    /**
     * @param ElementtypeStructure $fromStructure
     * @param ElementtypeStructure $toStructure
     *
     * @return Diff
     */
    public function diff(ElementtypeStructure $fromStructure, ElementtypeStructure $toStructure)
    {
        $diff = new Diff();

        $this->doDiff($diff, $fromStructure, $toStructure);

        return $diff;
    }

    /**
     * @param Diff                 $diff
     * @param ElementtypeStructure $fromStructure
     * @param ElementtypeStructure $toStructure
     *
     * @return Diff
     */
    private function doDiff(Diff $diff, ElementtypeStructure $fromStructure = null, ElementtypeStructure $toStructure = null)
    {
        $rii = new \RecursiveIteratorIterator($fromStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $fromNode) {
            /* @var $fromNode ElementtypeStructureNode */

            if ($toNode = $toStructure->getNode($fromNode->getDsId())) {
                if (implode('_', $toNode->getRepeatableDsIdPath()) !== implode('_', $fromNode->getRepeatableDsIdPath())) {
                    $diff->addMoved($fromNode, $toNode);
                }
            } else {
                $diff->addRemoved($fromNode);
            }
        }

        $rii = new \RecursiveIteratorIterator($toStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $toNode) {
            /* @var $toNode \Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode */

            if (!$fromStructure->getNode($toNode->getDsId())) {
                $diff->addAdded($toNode);
            }
        }

        return $diff;
    }
}
