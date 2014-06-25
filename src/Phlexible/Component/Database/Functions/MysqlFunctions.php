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
 * Database function abstraction for MySQL
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class MysqlFunctions extends AbstractFunctions
{
    /**
     * {@inheritdoc}
     */
    public function now()
    {
        return $this->expr('NOW()');
    }

    /**
     * {@inheritdoc}
     */
    public function concat($literal)
    {
        $literals = array();
        foreach (func_get_args() as $literal) {
            if ($literal instanceof Expression) {
                $literals[] = (string) $literal;
            } else {
                $literals[] = "'" . $literal . "'";
            }
        }
        $literals = implode(',', $literals);

        return $this->expr(sprintf("CONCAT(%s)", $literals));
    }

    /**
     * {@inheritdoc}
     */
    public function dateAdd($date, $unit, $expr)
    {
        if (!$date) {
            $date = 'NOW()';
        } elseif (!$date instanceof Expression) {
            $date = "'" . $date . "'";
        }

        return $this->expr(sprintf("DATE_ADD(%s, INTERVAL %s %s)", $date, $expr, strtoupper($unit)));
    }

    /**
     * {@inheritdoc}
     */
    public function dateSub($date, $unit, $expr)
    {
        if (!$date) {
            $date = 'NOW()';
        } elseif (!$date instanceof Expression) {
            $date = "'" . $date . "'";
        }

        return $this->expr(sprintf("DATE_SUB(%s, INTERVAL %s %s)", $date, $expr, strtoupper($unit)));
    }

    /**
     * {@inheritdoc}
     */
    public function unixTime($expr = '')
    {
        if ($expr && !$expr instanceof Expression) {
            $expr = "'" . $expr . "'";
        }

        return $this->expr(sprintf("UNIX_TIMESTAMP(%s)", $expr));
    }

    /**
     * {@inheritdoc}
     */
    public function strpos($haystack, $needle)
    {
        if (!$haystack instanceof Expression) {
            $haystack = "'" . $haystack . "'";
        }
        if (!$needle instanceof Expression) {
            $needle = "'" . $needle . "'";
        }

        return $this->expr(sprintf("INSTR(%s, %s)", $haystack, $needle));
    }
}
