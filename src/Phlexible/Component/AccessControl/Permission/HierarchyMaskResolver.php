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

            if ($ace->getObjectIdentifier() == $currentIdentifier) {
                $mask = $ace->getMask();
                $stopMask = $ace->getStopMask();
                $noInheritMask = $ace->getNoInheritMask();
            } else {
                $parentMask = $ace->getMask();
                $parentStopMask = $ace->getStopMask();
                $parentNoInheritMask = $ace->getNoInheritMask();
            }

            if ($ace->getMask() !== null) {
                if ($effectiveMask !== null) {
                    $effectiveMask = $effectiveMask | (int) $ace->getMask();
                } else {
                    $effectiveMask = (int) $ace->getMask();
                }
            }

            if ($effectiveMask && $ace->getStopMask() !== null) {
                // apply stop mask
                $effectiveMask = $effectiveMask ^ (int) $ace->getStopMask();
            }

            if ($effectiveMask && $ace->getObjectIdentifier() != $currentIdentifier && $ace->getNoInheritMask() !== null) {
                // apply no inherit mask
                $effectiveMask = $effectiveMask ^ (int) $ace->getNoInheritMask();
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
