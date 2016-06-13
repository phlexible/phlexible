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
     * @return int
     */
    public function resolve(array $path, $currentIdentifier)
    {
        if (!count($path)) {
            return 0;
        }

        $mask = null;

        while (count($path)) {
            $ace = array_shift($path);
            /* @var $ace Entry */

            $currentMask = (int) $ace->getMask();
            if ($ace->getObjectIdentifier() == $currentIdentifier && $ace->getStopMask()) {
                // apply stop mask
                $currentMask = $currentMask ^ (int) $ace->getStopMask();
            }
            if ($ace->getObjectIdentifier() != $currentIdentifier && $ace->getNoInheritMask()) {
                // apply no inherit mask
                $currentMask = $currentMask ^ (int) $ace->getNoInheritMask();
            }
            if ($mask === null) {
                $mask = $currentMask;
            } else {
                $mask = $currentMask & $mask;
            }
        }

        return $mask;
    }
}
