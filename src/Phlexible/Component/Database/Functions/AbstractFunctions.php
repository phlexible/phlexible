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
 * Database function abstraction
 *
 * @author Phillip Look <pl@brainbits.net>
 */
abstract class AbstractFunctions implements FunctionsInterface
{
    /**
     * {@inheritdoc}
     */
    public function expr($expression)
    {
        return new Expression($expression);
    }
}
