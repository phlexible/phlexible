<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Permission;

use Phlexible\Component\AccessControl\Domain\Entry;

/**
 * Class HierarchyMaskResolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class HierarchyMaskResolver
{
    /**
     * @param Entry[] $path
     * @param string  $currentIdentifier
     *
     * @return array
     */
    public function resolve(array $path, $currentIdentifier)
    {
        if (!count($path)) {
            return 0;
        }

        $effectiveMask = null;
        $mask = null;
        $stopMask = null;
        $noInheritMask = null;
        $parentMask = null;
        $parentStopMask = null;
        $parentNoInheritMask = null;

        while (count($path)) {
            $ace = array_shift($path);
            /* @var $ace Entry */

            $currentMask = (int) $ace->getMask();

            if ($ace->getObjectIdentifier() == $currentIdentifier) {
                $mask = (int) $ace->getMask();
                $stopMask = (int) $ace->getStopMask();
                $noInheritMask = (int) $ace->getNoInheritMask();
            } else {
                $parentMask = (int) $ace->getMask();
                $parentStopMask = (int) $ace->getStopMask();
                $parentNoInheritMask = (int) $ace->getNoInheritMask();
            }

            if ($ace->getObjectIdentifier() == $currentIdentifier && $ace->getStopMask()) {
                // apply stop mask
                $currentMask = $currentMask ^ (int) $ace->getStopMask();
            }

            if ($ace->getObjectIdentifier() != $currentIdentifier && $ace->getNoInheritMask()) {
                // apply no inherit mask
                $currentMask = $currentMask ^ (int) $ace->getNoInheritMask();
            }

            if ($effectiveMask === null) {
                $effectiveMask = (int) $currentMask;
            } else {
                $effectiveMask = (int) ($currentMask & $effectiveMask);
            }
        }

        return array(
            'effectiveMask' => $effectiveMask,
            'mask' => $mask,
            'stopMask' => $stopMask,
            'noInheritMask' => $noInheritMask,
            'parentMask' => $parentMask,
            'parentStopMask' => $parentStopMask,
            'parentNoInheritMask' => $parentNoInheritMask,
        );
    }
}
