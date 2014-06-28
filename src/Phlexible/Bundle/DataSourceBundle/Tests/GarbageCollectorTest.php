<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Tests;

use Phlexible\Bundle\GuiBundle\Util\Uuid;

class MWF_Core_DataSources_GarbageCollectorTest extends \PHPUnit_Framework_TestCase
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
     * run() with single id should call internal run with array
     */
    public function testRunWithSingleIdShouldCallInternalRunWithArray()
    {
        // SETUP
        $singleId = Uuid::generate();

        $garbageCollector = $this->createGarbageCollectorMock(array('_run'));

        $garbageCollector->expects($this->once())
                         ->method('_run')
                         ->with($this->equalTo(array($singleId)));

        // EXERCISE
        $garbageCollector->run($singleId);
    }

    /**
     * run() with multiple ids should call internal run with array
     */
    public function testRunWithMultipleIdsShouldCallInternalRunWithArray()
    {
        // SETUP
        $multipleIds = $this->createArrayWithDataSourceIds();

        $garbageCollector = $this->createGarbageCollectorMock(array('_run'));

        $garbageCollector->expects($this->once())
                         ->method('_run')
                         ->with($this->equalTo($multipleIds));

        // EXERCISE
        $garbageCollector->run($multipleIds);
    }

    /**
     * run() without arguments should fetch all ids from repository.
     */
    public function testRunWithWithoutArgumentsShouldFetchAllIdsFromRepository()
    {
        // SETUP
        $dataSourceIds = $this->createArrayWithDataSourceIds();

        $repository       = $this->createRepositoryMock();
        $garbageCollector = $this->createGarbageCollectorMock(array('_run'), $repository);

        $repository->expects($this->any())
                   ->method('getAllDataSourceIds')
                   ->will($this->returnValue($dataSourceIds));

        $garbageCollector->expects($this->once())
                         ->method('_run')
                         ->with($this->equalTo($dataSourceIds));

        // EXERCISE
        $garbageCollector->run();
    }

    /**
     * Create a MWF_Core_DataSources_Repository mock object.
     *
     * @param array $allDataSourceIds ids returned by getAllDataSourceIds() [Optional]
     *
     * @return MWF_Core_DataSources_Repository
     */
    public function createRepositoryMock(array $allDataSourceIds = array())
    {
        $repository = $this->getMock('MWF_Core_DataSources_Repository', array(), array(), '', false);

        return $repository;
    }

    public function createGarbageCollectorMock(array $methods = array(),
                                               MWF_Core_DataSources_Repository $repository = null)
    {
        $dispatcher = $this->getMock('Brainbits_Event_Dispatcher', array(), array(), '', false);

        if (null === $repository) {
            $repository = $this->createRepositoryMock();
        }

        $garbageCollectorMock = $this->getMock(
            'MWF_Core_DataSources_GarbageCollector',
            $methods,
            array($dispatcher, $repository)
        );

        return $garbageCollectorMock;
    }

    /**
     * Create an array with data source ids.
     *
     * @param int $size
     *
     * @return array
     */
    public function createArrayWithDataSourceIds($size = 2)
    {
        $ids = array();

        for ($i = 0; $i < $size; ++$i) {
            $ids[] = Uuid::generate();
        }

        return $ids;
    }
}
