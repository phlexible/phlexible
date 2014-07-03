<?php

namespace Phlexible\Component\Database\Tests;

use Phlexible\Component\Database\ConnectionManager;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Connection manager test case.
 */
class ConnectionManagerTest extends TestCase
{
    /**
     * Add new connection using __set() / __get().
     */
    public function testAddConnectionUsingMagicGetterSetter()
    {
        $pool = $this->createManager();
        $database = $this->createDummyConnection();

        // set new adapter in pool
        $pool->default = $database;

        $this->assertSame($database, $pool->default);
    }

    /**
     * Add new connection using __call().
     */
    public function testAddNewConnectionUsingMagicCall()
    {
        $pool = $this->createManager();
        $database = $this->createDummyConnection();

        // set new adapter in pool
        $pool->setDefault($database);

        $this->assertSame($database, $pool->getDefault());
    }

    /**
     * Calling undefined method throws exception.
     *
     * @expectedException BadMethodCallException
     */
    public function testCallingUndefinedMethodThrowsException()
    {
        $pool = $this->createManager();

        // set new adapter in pool
        $pool->unknownMethod();
    }

    /**
     * Calling setter without parameter throws BadMethodCallException.
     *
     * @expectedException \BadMethodCallException
     */
    public function testCallingSetterWithoutArgumentThrowsException()
    {
        $pool = $this->createManager();

        // set new adapter in pool
        $pool->setRead();
    }

    /**
     * Accessing unknown connection name throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAccessingUnknownConnectionNameThrowsException()
    {
        $pool = $this->createManager();

        // access unknown connection
        $pool->unknown;
    }

    /**
     * Create connection manager.
     *
     * @return ConnectionManager
     */
    protected function createManager()
    {
        return new ConnectionManager();
    }

    /**
     * Get a dummy db adapter.
     *
     * @return \Zend_Db_Adapter_Abstract
     */
    protected function createDummyConnection()
    {
        static $database = null;

        if (null === $database) {
            $database = new \Zend_Test_DbAdapter();
        }

        return $database;
    }

}
