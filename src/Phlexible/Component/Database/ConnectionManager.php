<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Database;

use Phlexible\Component\Database\Exception\BadMethodCallException;
use Phlexible\Component\Database\Exception\InvalidArgumentException;
use Phlexible\Component\Database\Functions\FunctionsInterface;
use Zend_Db_Adapter_Abstract as Connection;

/**
 * Database connection manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @property Connection $default
 * @property FunctionsInterface $fn
 */
class ConnectionManager
{
    /**
     * @var Connection[]
     */
    protected $connections = array();

    /**
     * @param Connection[] $connections
     */
    public function __construct(array $connections = array())
    {
        foreach ($connections as $connectionName => $connection) {
            $connectionName = substr($connectionName, 18);
            $this->set($connectionName, $connection);
        }
    }

    /**
     * Get database connection.
     *
     * @param string $name connection name
     *
     * @return Connection
     * @throws InvalidArgumentException if connection name is unknown
     */
    public function __get($name)
    {
        $name = strtolower($name);

        if (in_array($name, array('read', 'write'))) {
            $name = 'default';
        }

        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        throw new InvalidArgumentException("Connection $name not in connection pool.");
    }

    /**
     * @return Connection[]
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * @param string                    $name
     * @param \Zend_Db_Adapter_Abstract $connection
     */
    public function set($name, Connection $connection)
    {
        $name = strtolower($name);

        $this->connections[$name] = $connection;
    }

    /**
     * @param string                    $name
     * @param \Zend_Db_Adapter_Abstract $connection
     */
    public function __set($name, Connection $connection)
    {
        $this->set($name, $connection);
    }

    /**
     * Magic methods
     *  - Zend_Db_Adapter_Abstract getXxxx()
     *  - Manager setXxxx(Zend_Db_Adapter_Abstract $db) (fluent interface)
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call($name, array $arguments)
    {
        $prefix         = substr($name, 0, 3);
        $connectionName = substr($name, 3);

        if ('get' === $prefix) {
            return $this->$connectionName;
        } elseif ('set' === $prefix) {
            if (!count($arguments)) {
                throw new BadMethodCallException('Missing parameter: ' . __CLASS__ . '::' . $name);
            }

            $this->$connectionName = $arguments[0];

            return $this;
        }

        throw new BadMethodCallException('Unknown method: ' . __CLASS__ . '::' . $name);
    }

    /**
     * Check if connection exists.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->connections[$name]);
    }
}
