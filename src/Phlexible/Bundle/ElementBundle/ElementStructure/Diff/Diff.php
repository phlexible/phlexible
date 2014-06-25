<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementStructure\Diff;

use Phlexible\Bundle\ElementBundle\ElementStructure\ElementStructure;

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
        foreach ($from->getValues() as $value) {

        }

        return $to;
    }
}