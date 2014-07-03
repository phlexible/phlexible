<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Database\Functions;

use Phlexible\Component\Database\Exception\NotSupportedException;
use Zend_Db_Expr as Expression;

/**
 * Database function abstraction for Sqlite
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class SqliteFunctions extends AbstractFunctions
{
    /**
     * {@inheritdoc}
     */
    public function now()
    {
        return $this->expr('DATETIME("NOW")');
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
            $date = 'NOW';
        }

        return $this->expr(sprintf("DATETIME('%s', '+%s %s')", $date, $expr, $unit));
    }

    /**
     * {@inheritdoc}
     */
    public function dateSub($date, $unit, $expr)
    {
        if (!$date) {
            $date = 'NOW';
        }

        return $this->expr(sprintf("DATETIME('%s', '%s %s')", $date, $expr, $unit));
    }

    /**
     * {@inheritdoc}
     */
    public function unixTime($baseTime = '')
    {
        if (!$baseTime) {
            $baseTime = 'NOW';
        }

        return $this->expr(sprintf("STRFTIME('%%s', '%s')", $baseTime));
    }

    /**
     * {@inheritdoc}
     */
    public function strpos($haystack, $needle)
    {
        throw new NotSupportedException('strpos() not yet supported in sqlite.');
    }
}
