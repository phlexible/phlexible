<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Permission;

use Phlexible\Bundle\AccessControlBundle\Entity\AccessControlEntry;

/**
 * Class HierarchyMaskResolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class HierarchyMaskResolver
{
    /**
     * @param AccessControlEntry[] $path
     *
     * @return int
     */
    public function resolve(array $path)
    {
        $first = true;
        $mask = 0;

        while (count($path)) {
            $ace = array_shift($path);

            $currentMask = $ace->getMask();
            if (!$first && $ace->getStopMask()) {
                // apply stop mask
                $currentMask = $currentMask ^ $ace->getStopMask();
            }
            if (count($path) && $ace->getNoInheritMask()) {
                // apply no inherit mask
                $currentMask = $currentMask ^ $ace->getNoInheritMask();
            }
            $mask = $currentMask ^ $mask;
            $first = false;
        }

        return $mask;
    }
}
