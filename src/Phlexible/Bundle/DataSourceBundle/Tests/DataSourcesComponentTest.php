<?php

/**
 * MWF - MAKEweb Framework
 *
 * PHP Version 5
 *
 * @category    MWF
 * @package     MWF_Test_Core_DataSources
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Generator.php 2312 2007-01-25 18:46:27Z swentz $
 */

/**
 * Test: MWF_Core_DataSources_Component
 *
 * @category    MWF
 * @package     MWF_Test_Core_DataSources
 * @author      Phillip Look <pl@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class DataSourceBundleTest extends PHPUnit_Framework_TestCase
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
     * Contructor
     */
    public function testConstructor()
    {
        // EXERCISE
        $component = new MWF_Core_DataSources_Component();

        // VERIFY
        $this->assertTrue(
            $component instanceof MWF_Core_DataSources_Component,
            'object not created correctly'
        );
    }

    /**
     * Create database structure
     */
    public function testCreateDataBase()
    {
        // SETUP
        $dbAdapter = Brainbits_Test_Db_Helper::createAdapterPdoSqliteMemory();
        $component = new MWF_Core_DataSources_Component();

        // EXERCISE
        $component = MWF_Test_Component_Helper::setupDb($component, $dbAdapter);
    }
}
