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
 * Database function abstraction for Postgres
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class PgsqlFunctions extends AbstractFunctions
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
        $literals = implode(' || ', $literals);

        return $this->expr($literals);
    }

    /**
     * {@inheritdoc}
     */
    public function dateAdd($date, $unit, $expr)
    {
        if (!$date) {
            $date = 'NOW()';
        }

        return $this->expr(sprintf("%s + interval '%s' %s", $date, $expr, $unit));
    }

    /**
     * {@inheritdoc}
     */
    public function dateSub($date, $unit, $expr)
    {
        if (!$date) {
            $date = 'NOW()';
        }

        return $this->expr(sprintf("%s - interval '%s' %s", $date, $expr, $unit));
    }

    /**
     * {@inheritdoc}
     */
    public function unixTime($expr = '')
    {
        return $this->expr(sprintf("EXTRACT(EPOCH FROM (%s))", $expr));
    }

    /**
     * {@inheritdoc}
     */
    public function strpos($haystack, $needle)
    {
        return $this->expr(sprintf("STRPOS('%s', '%s')", $needle, $haystack));
    }
}
