<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Formatter;

/**
 * Age formatter
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class AgeFormatter
{
    /**
     * Format the difference of 2 dates in a human readable form
     * If the second date is omitted, the current time will be used
     *
     * @param string  $firstDate
     * @param string  $secondDate
     * @param boolean $returnAsArray
     *
     * @return string|array
     */
    public static function formatDate($firstDate, $secondDate = null, $returnAsArray = false)
    {
        if ($firstDate instanceof \DateTime) {
            $firstTimestamp = $firstDate->format('U');
        } elseif (is_int($firstDate)) {
            $firstTimestamp = strtotime($firstDate);
        } else {
            $firstTimestamp = strtotime($firstDate);
        }

        $secondTimestamp = null;
        if ($secondDate instanceof \DateTime) {
            $secondTimestamp = $secondDate->format('U');
        } elseif (is_int($secondDate)) {
            $secondTimestamp = strtotime($secondDate);
        } else {
            $secondTimestamp = strtotime($secondDate);
        }

        return self::formatTimeStamp($firstTimestamp, $secondTimestamp, $returnAsArray);
    }

    /**
     * Format the difference of 2 unix timestamps in a human readable form
     * If the second date is omitted, the current time will be used
     *
     * @param string  $firstTimestamp
     * @param string  $secondTimestamp
     * @param boolean $returnAsArray
     *
     * @return string|array
     */
    public static function formatTimestamp($firstTimestamp, $secondTimestamp = null, $returnAsArray = false)
    {
        if ($secondTimestamp === null) {
            $secondTimestamp = time();
        }

        $startDiff = $diff = abs($firstTimestamp - $secondTimestamp);

        $suffixes = array(
            array('single' => 'second', 'multi' => 'seconds', 'div' => 60, 'cap' => 60),
            array('single' => 'minute', 'multi' => 'minutes', 'div' => 3600, 'cap' => 60),
            array('single' => 'hour', 'multi' => 'hours', 'div' => 86400, 'cap' => 24),
            array('single' => 'day', 'multi' => 'days', 'div' => 604800, 'cap' => 7),
            array('single' => 'week', 'multi' => 'weeks', 'div' => 2629743.83, 'cap' => 4),
            array('single' => 'month', 'multi' => 'months', 'div' => 31556925, 'cap' => 12),
            array('single' => 'year', 'multi' => 'years', 'div' => 0, 'cap' => 0),
        );

        foreach ($suffixes as $suffixRow) {
            $single = $suffixRow['single'];
            $multi = $suffixRow['multi'];
            $div = $suffixRow['div'];
            $cap = $suffixRow['cap'];

            if (!$cap) {
                break;
            }

            if ($diff < $cap) {
                if ($returnAsArray) {
                    return array($diff, ($diff === 1 ? $single : $multi));
                }

                return $diff . ' ' . ($diff === 1 ? $single : $multi);
            }

            $diff = (int) round($startDiff / $div);
        }

        if ($returnAsArray) {
            return array($diff, ($diff === 1 ? $single : $multi));
        }

        return $diff . ' ' . ($diff === 1 ? $single : $multi);
    }
}
