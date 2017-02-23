<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Mime\Tests\Adapter;

use Phlexible\Component\Mime\Adapter\AdapterInterface;
use Phlexible\Component\Mime\Adapter\CompositeAdapter;

/**
 * Composite adapter test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @covers \Phlexible\Component\Mime\Adapter\CompositeAdapter
 */
class CompositeAdapterTest extends AbstractAdapterTest
{
    public function testCheckAvailability()
    {
        $adapterMock1 = $this->prophesize(AdapterInterface::class);
        $adapterMock1->isAvailable('testFile')->willReturn(true);

        $adapterMock2 = $this->prophesize(AdapterInterface::class);

        $adapter = $this->createAdapter([$adapterMock1->reveal(), $adapterMock2->reveal()]);

        $this->assertTrue($adapter->isAvailable('testFile'));
    }

    public function testIsAvailableReturnsFalseOnEmptyAdapters()
    {
        $this->assertFalse($this->createAdapter()->isAvailable('testFile'));
    }

    public function testGetMimetypeReturnsNullOnEmptyAdapters()
    {
        $this->assertNull($this->createAdapter()->getInternetMediaTypeStringFromFile('test'));
    }

    public function testIsAvailableIteratesOverConstructorAdapters()
    {
        $adapterMock1 = $this->prophesize(AdapterInterface::class);
        $adapterMock1->isAvailable('testFile')->willReturn(false);

        $adapterMock2 = $this->prophesize(AdapterInterface::class);
        $adapterMock2->isAvailable('testFile')->willReturn(false);

        $adapter = $this->createAdapter([$adapterMock1->reveal(), $adapterMock2->reveal()]);

        $this->assertFalse($adapter->isAvailable('testFile'));
    }

    public function testIsAvailableIteratesOverAllAddedAdapters()
    {
        $adapterMock1 = $this->prophesize(AdapterInterface::class);
        $adapterMock1->isAvailable('testFile')->willReturn(false);

        $adapterMock2 = $this->prophesize(AdapterInterface::class);
        $adapterMock2->isAvailable('testFile')->willReturn(false);

        $adapter = $this->createAdapter();
        $adapter->setAdapters([$adapterMock1->reveal(), $adapterMock2->reveal()]);

        $this->assertFalse($adapter->isAvailable('testFile'));
    }

    public function testIsAvailableBreaksOnFirstAvailableAdapter()
    {
        $adapterMock1 = $this->prophesize(AdapterInterface::class);
        $adapterMock1->isAvailable('testFile')->willReturn(true);

        $adapterMock2 = $this->prophesize(AdapterInterface::class);

        $adapter = $this->createAdapter();
        $adapter->setAdapters([$adapterMock1->reveal(), $adapterMock2->reveal()]);

        $this->assertTrue($adapter->isAvailable('testFile'));
    }

    public function testGetMimetypesIteratesOverAdapters()
    {
        $adapterMock1 = $this->prophesize(AdapterInterface::class);
        $adapterMock1->isAvailable('test')->willReturn(true);
        $adapterMock1->getInternetMediaTypeStringFromFile('test')->willReturn(false);

        $adapterMock2 = $this->prophesize(AdapterInterface::class);
        $adapterMock2->isAvailable('test')->willReturn(true);
        $adapterMock2->getInternetMediaTypeStringFromFile('test')->willReturn('test');

        $adapter = $this->createAdapter();
        $adapter->setAdapters([$adapterMock1->reveal(), $adapterMock2->reveal()]);

        $this->assertEquals('test', $adapter->getInternetMediaTypeStringFromFile('test'));
    }

    /**
     * {@inheritdoc}
     */
    protected function createAdapter(array $adapters = [])
    {
        return new CompositeAdapter($adapters);
    }
}
