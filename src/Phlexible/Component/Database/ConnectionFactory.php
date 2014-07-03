<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Database;

use Phlexible\Component\Database\Functions\FunctionsInterface;
use Zend_Db_Adapter_Abstract as Connection;

/**
 * Database connection manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @property Connection         $default
 * @property FunctionsInterface $fn
 */
class ConnectionFactory
{
    /**
     * @param string $type
     * @param array  $parameters
     *
     * @return Connection
     */
    public static function createConnection($type, $parameters)
    {
        $connection = \Zend_Db::factory($type, $parameters);

        // set Fetch Mode
        $connection->setFetchMode(\Zend_Db::FETCH_ASSOC);

        $connection->prefix = $parameters['prefix'];

        $functionsClass = sprintf(
            'Phlexible\Component\Database\Functions\%sFunctions',
            ucfirst(strtolower($parameters['type']))
        );
        $connection->fn = new $functionsClass();

        return $connection;
    }
}
