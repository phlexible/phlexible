<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Database\Functions;

use Zend_Db_Expr as Expression;

/**
 * Database function abstraction interface
 *
 * @author Phillip Look <pl@brainbits.net>
 */
interface FunctionsInterface
{
    /**
     * @param string $expression
     *
     * @return Expression
     */
    public function expr($expression);

    /**
     * Current time expression
     * Function returns a string
     *
     * @return Expression
     */
    public function now();

    /**
     * Concat strings
     *
     * @param string $literal
     *
     * @return Expression
     */
    public function concat($literal);

    /**
     * Date addition expression
     * Function returns a string
     *
     * @param string $date Date
     * @param string $unit Part
     * @param string $expr Interval
     *
     * @return Expression
     */
    public function dateAdd($date, $unit, $expr);

    /**
     * Date subtraction expression
     * Function returns a string
     *
     * @param string $date Date
     * @param string $unit Part
     * @param string $expr Interval
     *
     * @return Expression
     */
    public function dateSub($date, $unit, $expr);

    /**
     * Unix timestamp expression
     * Function returns a string
     *
     * @param string $expr Date
     *
     * @return Expression
     */
    public function unixTime($expr = '');

    /**
     * Returns the position of the first occurrence of substring $needle in string $haystack.
     * If $needle is not found, it will return 0.
     * Function returns an integer
     *
     * @param string $haystack Haystack
     * @param string $needle   Needle
     *
     * @return Expression
     */
    public function strpos($haystack, $needle);
}
