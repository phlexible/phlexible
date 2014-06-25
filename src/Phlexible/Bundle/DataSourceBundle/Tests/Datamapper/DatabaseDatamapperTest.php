<?php

/**
 * MWF - MAKEweb Framework
 *
 * PHP Version 5
 *
 * @category    MWF
 * @package     MWF_Core_DataSources
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Generator.php 2312 2007-01-25 18:46:27Z swentz $
 */

/**
 * Test: MWF_Core_DataSources_DataMapper_Database
 *
 * @category    MWF
 * @package     MWF_Core_DataSources
 * @author      Phillip Look <pl@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class MWF_Core_DataSources_DataMapper_DatabaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * toArray() returns empty array for autoinitialized collection
     */
    public function testConstructor()
    {
        // SETUP
        $dbPool = MWF_Test_Db_Helper::createPoolDummy();

        // EXERCISE
        $dataMapper = new MWF_Core_DataSources_DataMapper_Database($dbPool);

        // VERIFY
        $this->assertTrue(
            $dataMapper instanceof MWF_Core_DataSources_DataMapper_Database,
            'constructor does not create correct type'
        );
    }
}
