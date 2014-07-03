<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Tests;

use Phlexible\Bundle\DataSourceBundle\DataSourceEvents;
use Phlexible\Bundle\DataSourceBundle\Entity\DataSource;
use Phlexible\Bundle\DataSourceBundle\GarbageCollector\GarbageCollector;
use Phlexible\Bundle\DataSourceBundle\Model\DataSourceManagerInterface;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * DataSource Test
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class GarbageCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GarbageCollector
     */
    private $garbageCollector;

    /**
     * @var DataSourceManagerInterface|MockObject
     */
    private $managerMock;

    /**
     * @var EventDispatcherInterface|MockObject
     */
    private $dispatcherMock;

    public function setUp()
    {
        $this->dispatcherMock = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
        $this->managerMock = $this->getMockBuilder('Phlexible\Bundle\DataSourceBundle\Model\DataSourceManagerInterface')->getMock();
        $this->garbageCollector = new GarbageCollector($this->managerMock, $this->dispatcherMock);

        $this->dispatcherMock
            ->expects($this->any())
            ->method('dispatch')
            ->will($this->returnValue(new Event()));

        $this->managerMock
            ->expects($this->any())
            ->method('getAllDataSourceLanguages')
            ->will($this->returnValue(array('de')));

        $datasource = new DataSource();

        $this->managerMock
            ->expects($this->any())
            ->method('find')
            ->will($this->returnValue($datasource));
    }

    public function testEvents()
    {
        $this->dispatcherMock
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(DataSourceEvents::BEFORE_DELETE_VALUES);

        $this->dispatcherMock
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(DataSourceEvents::DELETE_VALUES);

        $this->dispatcherMock
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(DataSourceEvents::BEFORE_MARK_ACTIVE);

        $this->dispatcherMock
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(DataSourceEvents::MARK_ACTIVE);

        $this->dispatcherMock
            ->expects($this->at(4))
            ->method('dispatch')
            ->with(DataSourceEvents::BEFORE_MARK_INACTIVE);

        $this->dispatcherMock
            ->expects($this->at(5))
            ->method('dispatch')
            ->with(DataSourceEvents::MARK_INACTIVE);

        $this->garbageCollector->run(Uuid::generate());
    }

    /**
     * run() with single id should call internal run with array
     */
    public function testRunWithSingleIdShouldCallInternalRunWithArray()
    {
        $result = $this->garbageCollector->run(Uuid::generate());

        $this->assertEquals(array('candidates' => 1, 'removed' => 0, 'activated' => 0, 'deactivated' => 0), $result);
    }

    /**
     * run() with multiple ids should call internal run with array
     */
    public function testRunWithMultipleIdsShouldCallInternalRunWithArray()
    {
        $multipleIds = $this->createArrayWithDataSourceIds(5);

        $result = $this->garbageCollector->run($multipleIds);

        $this->assertEquals(array('candidates' => 5, 'removed' => 0, 'activated' => 0, 'deactivated' => 0), $result);
    }

    /**
     * run() without arguments should fetch all ids from repository.
     */
    public function testRunWithWithoutArgumentsShouldFetchAllIdsFromRepository()
    {
        $dataSourceIds = $this->createArrayWithDataSourceIds(10);

        $this->managerMock
            ->expects($this->any())
            ->method('getAllDataSourceIds')
            ->will($this->returnValue($dataSourceIds));

        $result = $this->garbageCollector->run();

        $this->assertEquals(array('candidates' => 10, 'removed' => 0, 'activated' => 0, 'deactivated' => 0), $result);
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
