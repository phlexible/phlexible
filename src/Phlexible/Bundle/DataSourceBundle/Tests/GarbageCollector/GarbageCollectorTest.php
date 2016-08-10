<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Tests\GarbageCollector;

use Phlexible\Bundle\DataSourceBundle\DataSourceEvents;
use Phlexible\Bundle\DataSourceBundle\Entity\DataSource;
use Phlexible\Bundle\DataSourceBundle\Entity\DataSourceValueBag;
use Phlexible\Bundle\DataSourceBundle\Event\GarbageCollectEvent;
use Phlexible\Bundle\DataSourceBundle\GarbageCollector\GarbageCollector;
use Phlexible\Bundle\DataSourceBundle\Model\DataSourceManagerInterface;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Garbage collector test
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
     * @var DataSourceManagerInterface|ObjectProphecy
     */
    private $manager;

    /**
     * @var EventDispatcherInterface|ObjectProphecy
     */
    private $eventDispatcher;

    /**
     * @var DataSource
     */
    private $datasource;

    public function setUp()
    {

        $this->eventDispatcher = new EventDispatcher();
        $this->manager = $this->prophesize('Phlexible\Bundle\DataSourceBundle\Model\DataSourceManagerInterface');
        $this->garbageCollector = new GarbageCollector($this->manager->reveal(), $this->eventDispatcher);

        $this->datasource = new DataSource();
        $this->datasource->setTitle('testDatasource');
        $values = new DataSourceValueBag();
        $values->setLanguage('de');
        $this->datasource->addValueBag($values);
    }

    public function testEvents()
    {
        $fired = 0;
        $this->eventDispatcher->addListener(
            DataSourceEvents::BEFORE_GARBAGE_COLLECT,
            function() use (&$fired) {
                $fired++;
            }
        );
        $this->eventDispatcher->addListener(
            DataSourceEvents::GARBAGE_COLLECT,
            function() use (&$fired) {
                $fired++;
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn([$this->datasource]);
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $this->garbageCollector->run();

        $this->assertSame(2, $fired);
    }

    public function testRunWithNoValues()
    {
        $this->manager->findBy(Argument::cetera())->willReturn([$this->datasource]);
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run();

        $this->assertCount(0, $result['testDatasource']['de']['remove']);
        $this->assertCount(0, $result['testDatasource']['de']['active']);
        $this->assertCount(0, $result['testDatasource']['de']['inactive']);
    }

    public function testRunRemovesUnusedValuesInModeRemoveUnused()
    {
        $this->datasource->addValueForLanguage('de', 'value1');
        $this->datasource->addValueForLanguage('de', 'value2');

        $this->manager->findBy(Argument::cetera())->willReturn([$this->datasource]);
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            [
                'active' => [],
                'inactive' => [],
                'remove' => ['value1', 'value2'],
            ],
            $result['testDatasource']['de']
        );
    }

    public function testRunRemovesUnusedValuesInModeUnusedAndInactive()
    {
        $this->datasource->addValueForLanguage('de', 'value1');
        $this->datasource->addValueForLanguage('de', 'value2');

        $this->manager->findBy(Argument::cetera())->willReturn([$this->datasource]);
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED_AND_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            [
                'active' => [],
                'inactive' => [],
                'remove' => ['value1', 'value2'],
            ],
            $result['testDatasource']['de']
        );
    }

    public function testRunMarksUnusedValuesAsInactiveInModeUnusedAndInactive()
    {
        $this->datasource->addValueForLanguage('de', 'value1');
        $this->datasource->addValueForLanguage('de', 'value2');

        $this->manager->findBy(Argument::cetera())->willReturn([$this->datasource]);
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_MARK_UNUSED_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            [
                'active' => [],
                'inactive' => ['value1', 'value2'],
                'remove' => [],
            ],
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsActiveValuesInModeRemoveUnused()
    {
        $this->datasource->addValueForLanguage('de', 'value1', false);
        $this->datasource->addValueForLanguage('de', 'value2', false);

        $this->eventDispatcher->addListener(
            DataSourceEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markActive(['value1', 'value2']);
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn([$this->datasource]);
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            [
                'active' => ['value1', 'value2'],
                'inactive' => [],
                'remove' => [],
            ],
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsActiveValuesInModeRemoveUnusedAndInactive()
    {
        $this->datasource->addValueForLanguage('de', 'value1', false);
        $this->datasource->addValueForLanguage('de', 'value2', false);

        $this->eventDispatcher->addListener(
            DataSourceEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markActive(['value1', 'value2']);
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn([$this->datasource]);
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED_AND_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            [
                'active' => ['value1', 'value2'],
                'inactive' => [],
                'remove' => [],
            ],
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsActiveValuesInModeMarkUnusedInactive()
    {
        $this->datasource->addValueForLanguage('de', 'value1', false);
        $this->datasource->addValueForLanguage('de', 'value2', false);

        $this->eventDispatcher->addListener(
            DataSourceEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markActive(['value1', 'value2']);
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn([$this->datasource]);
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_MARK_UNUSED_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            [
                'active' => ['value1', 'value2'],
                'inactive' => [],
                'remove' => [],
            ],
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsInactiveValuesInModeRemoveUnused()
    {
        $this->datasource->addValueForLanguage('de', 'value1', false);
        $this->datasource->addValueForLanguage('de', 'value2', false);

        $this->eventDispatcher->addListener(
            DataSourceEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markInactive(['value1', 'value2']);
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn([$this->datasource]);
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            [
                'active' => [],
                'inactive' => ['value1', 'value2'],
                'remove' => [],
            ],
            $result['testDatasource']['de']
        );
    }

    public function testRunRemovesInactiveValuesInModeRemoveUnused()
    {
        $this->datasource->addValueForLanguage('de', 'value1', false);
        $this->datasource->addValueForLanguage('de', 'value2', false);

        $this->eventDispatcher->addListener(
            DataSourceEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markInactive(['value1', 'value2']);
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn([$this->datasource]);
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED_AND_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            [
                'active' => [],
                'inactive' => [],
                'remove' => ['value1', 'value2'],
            ],
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsInactiveValuesInModeMarkUnusedInactive()
    {
        $this->datasource->addValueForLanguage('de', 'value1', false);
        $this->datasource->addValueForLanguage('de', 'value2', false);

        $this->eventDispatcher->addListener(
            DataSourceEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markInactive(['value1', 'value2']);
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn([$this->datasource]);
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_MARK_UNUSED_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            [
                'active' => [],
                'inactive' => ['value1', 'value2'],
                'remove' => [],
            ],
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsValuesAndRemovesUnusedInModeRemoveUnused()
    {
        $this->datasource->addValueForLanguage('de', 'value1');
        $this->datasource->addValueForLanguage('de', 'value2');
        $this->datasource->addValueForLanguage('de', 'value3');
        $this->datasource->addValueForLanguage('de', 'value4');
        $this->datasource->addValueForLanguage('de', 'value5');
        $this->datasource->addValueForLanguage('de', 'value6');

        $this->eventDispatcher->addListener(
            DataSourceEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markActive(['value1', 'value2']);
                $event->markInactive(['value3', 'value4']);
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn([$this->datasource]);
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            [
                'active' => ['value1', 'value2'],
                'inactive' => ['value3', 'value4'],
                'remove' => ['value5', 'value6'],
            ],
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsValuesAndRemovesUnusedInModeRemoveUnusedAndInactive()
    {
        $this->datasource->addValueForLanguage('de', 'value1');
        $this->datasource->addValueForLanguage('de', 'value2');
        $this->datasource->addValueForLanguage('de', 'value3');
        $this->datasource->addValueForLanguage('de', 'value4');
        $this->datasource->addValueForLanguage('de', 'value5');
        $this->datasource->addValueForLanguage('de', 'value6');

        $this->eventDispatcher->addListener(
            DataSourceEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markActive(['value1', 'value2']);
                $event->markInactive(['value3', 'value4']);
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn([$this->datasource]);
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_REMOVE_UNUSED_AND_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            [
                'active' => ['value1', 'value2'],
                'inactive' => [],
                'remove' => ['value3', 'value4', 'value5', 'value6'],
            ],
            $result['testDatasource']['de']
        );
    }

    public function testRunKeepsValuesAndRemovesUnusedInModeMarkUnusedInactive()
    {
        $this->datasource->addValueForLanguage('de', 'value1');
        $this->datasource->addValueForLanguage('de', 'value2');
        $this->datasource->addValueForLanguage('de', 'value3');
        $this->datasource->addValueForLanguage('de', 'value4');
        $this->datasource->addValueForLanguage('de', 'value5');
        $this->datasource->addValueForLanguage('de', 'value6');

        $this->eventDispatcher->addListener(
            DataSourceEvents::BEFORE_GARBAGE_COLLECT,
            function(GarbageCollectEvent $event) {
                $event->markActive(['value1', 'value2']);
                $event->markInactive(['value3', 'value4']);
            }
        );

        $this->manager->findBy(Argument::cetera())->willReturn([$this->datasource]);
        $this->manager->updateDataSource(Argument::any())->shouldBeCalled();

        $result = $this->garbageCollector->run(GarbageCollector::MODE_MARK_UNUSED_INACTIVE);

        $this->assertArrayHasKey('testDatasource', $result);
        $this->assertArrayHasKey('de', $result['testDatasource']);
        $this->assertSame(
            [
                'active' => ['value1', 'value2'],
                'inactive' => ['value3', 'value4', 'value5', 'value6'],
                'remove' => [],
            ],
            $result['testDatasource']['de']
        );
    }
}
