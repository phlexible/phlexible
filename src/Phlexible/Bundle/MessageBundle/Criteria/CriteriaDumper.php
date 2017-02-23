<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Criteria;

/**
 * Message criteria dumper.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CriteriaDumper
{
    /**
     * @param Criteria $criteria
     *
     * @return string
     */
    public function dump(Criteria $criteria)
    {
        $mode = strtoupper($criteria->getMode());

        $parts = array();
        foreach ($criteria as $criterium) {
            if ($criterium instanceof Criteria) {
                $parts[] = $this->dump($criterium, 1);
            } elseif ($criterium instanceof Criterium) {
                $type = $criterium->getType();
                $value = $criterium->getValue();
                if ($value instanceof \DateTime) {
                    $parts[] = $type.' = "'.$value->format('Y-m-d H:i:s').'"';
                } else {
                    $parts[] = $type.' = "'.$value.'"';
                }
            }
        }

        return '('.implode(' '.$mode.' ', $parts).')';
    }
}
