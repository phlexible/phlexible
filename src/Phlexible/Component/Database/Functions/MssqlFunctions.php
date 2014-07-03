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
 * Database function abstraction for MSSQL
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class MssqlFunctions extends AbstractFunctions
{
    /**
     * {@inheritdoc}
     */
    public function now()
    {
        return $this->expr('GETDATE()');
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
        $literals = implode(' + ', $literals);

        return $this->expr($literals);
    }

    /**
     * {@inheritdoc}
     */
    public function dateAdd($date, $unit, $expr)
    {
        if (!$date) {
            $date = 'GETDATE()';
        }

        return $this->expr(sprintf("DATEADD(%s, +%s, %s", $unit, $expr, $date));
    }

    /**
     * {@inheritdoc}
     */
    public function dateSub($date, $unit, $expr)
    {
        if (!$date) {
            $date = 'GETDATE()';
        }

        return $this->expr(sprintf("DATEADD(%s, -%s, %s", $unit, $expr, $date));
    }

    /**
     * {@inheritdoc}
     */
    public function unixTime($expr = '')
    {
        if (!$expr) {
            $expr = 'GETDATE()';
        } else {
            //$expr = "'" . $expr . "'"
        }

        return $this->expr(sprintf("DATEDIFF(s, '%s', %s)", "19700101", $expr));
    }

    /**
     * {@inheritdoc}
     */
    public function strpos($haystack, $needle)
    {
        throw new NotSupportedException('strpos() not yet supported in mssql.');
    }
}
