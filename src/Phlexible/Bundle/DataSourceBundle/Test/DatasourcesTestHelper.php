<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Test;

use Phlexible\Bundle\LockBundle\Datamapper\DatabaseDatamapper;

/**
 * Data sources test helper
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class DataSourcesTestHelper
{
    /**
     * Create pdo sqlite memory data mapper.
     *
     * @return DatabaseDatamapper
     */
    public static function createDataMapperPdoSqliteMemory()
    {
        $dbPool     = \MWF_Test_Db_Helper::createPoolPdoSqliteMemory();
        $datamapper = new DatabaseDatamapper($dbPool);

        return $datamapper;
    }
}