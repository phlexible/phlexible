<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\AccessControl\Permission;

use Phlexible\Component\AccessControl\Domain\Entry;

/**
 * Class HierarchyMaskResolver.
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

        $effectiveMask = 0;
        $mask = null;
        $stopMask = null;
        $noInheritMask = null;
        $parentMask = null;
        $parentStopMask = null;
        $parentNoInheritMask = null;

        $isFirst = true;

        while (count($path)) {
            /* @var $ace Entry */
            $ace = array_shift($path);

            $isLast = count($path) === 0;

            if ($isFirst) {
                $isFirst = false;
            } else {
                $parentMask = $effectiveMask;
                $parentStopMask = $stopMask;
                $parentNoInheritMask = $noInheritMask;

                if ($effectiveMask && $parentNoInheritMask !== null) {
                    // apply no inherit mask
                    $effectiveMask = $effectiveMask ^ (int) $parentNoInheritMask;
                }
            }

            $mask = $ace->getMask();
            $stopMask = $ace->getStopMask();
            $noInheritMask = $ace->getNoInheritMask();

            /*
            if ($ace->getObjectIdentifier() == $currentIdentifier) {
                $mask = $ace->getMask();
                $stopMask = $ace->getStopMask();
                $noInheritMask = $ace->getNoInheritMask();
            } else {
                $parentMask = $ace->getMask();
                $parentStopMask = $ace->getStopMask();
                $parentNoInheritMask = $ace->getNoInheritMask();
            }
            */

            if ($mask !== null) {
                $effectiveMask = $effectiveMask | (int) $mask;
            }

            if ($effectiveMask && $stopMask !== null) {
                // apply stop mask
                $effectiveMask = $effectiveMask ^ (int) $stopMask;
            }
        }

        return array(
            'effectiveMask' => $effectiveMask,
            'mask' => $mask !== null ? (int) $mask : null,
            'stopMask' => $stopMask !== null ? (int) $stopMask : null,
            'noInheritMask' => $noInheritMask !== null ? (int) $noInheritMask : null,
            'parentMask' => $parentMask !== null ? (int) $parentMask : null,
            'parentStopMask' => $parentStopMask !== null ? (int) $parentStopMask : null,
            'parentNoInheritMask' => $parentNoInheritMask !== null ? (int) $parentNoInheritMask : null,
        );
    }
}
