<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Mime\Tests\Adapter;

use Phlexible\Component\Mime\Adapter\CompositeAdapter;

/**
 * Composite adapter test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CompositeAdapterTest extends AbstractAdapterTest
{
    public function testCheckAvailability()
    {
        $adapterMock1 = $this->createAdapterMock();
        $adapterMock1
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(true));

        $adapterMock2 = $this->createAdapterMock();

        $adapter = $this->createAdapter([$adapterMock1, $adapterMock2]);

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
        $adapterMock1 = $this->createAdapterMock();
        $adapterMock1
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(false));

        $adapterMock2 = $this->createAdapterMock();
        $adapterMock2
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(false));

        $adapter = $this->createAdapter([$adapterMock1, $adapterMock2]);

        $this->assertFalse($adapter->isAvailable('testFile'));
    }

    public function testIsAvailableIteratesOverAllAddedAdapters()
    {
        $adapterMock1 = $this->createAdapterMock();
        $adapterMock1
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(false));

        $adapterMock2 = $this->createAdapterMock();
        $adapterMock2
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(false));

        $adapter = $this->createAdapter();
        $adapter->setAdapters([$adapterMock1, $adapterMock2]);

        $this->assertFalse($adapter->isAvailable('testFile'));
    }

    public function testIsAvailableBreaksOnFirstAvailableAdapter()
    {
        $adapterMock1 = $this->createAdapterMock();
        $adapterMock1
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(true));

        $adapterMock2 = $this->createAdapterMock();
        $adapterMock2
            ->expects($this->never())
            ->method('isAvailable');

        $adapter = $this->createAdapter();
        $adapter->setAdapters([$adapterMock1, $adapterMock2]);

        $this->assertTrue($adapter->isAvailable('testFile'));
    }

    public function testGetMimetypesIteratesOverAdapters()
    {
        $adapterMock1 = $this->createAdapterMock();
        $adapterMock1
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $adapterMock1
            ->expects($this->at(1))
            ->method('getInternetMediaTypeStringFromFile')
            ->with('test')
            ->will($this->returnValue(false));

        $adapterMock2 = $this->createAdapterMock();
        $adapterMock2
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $adapterMock2
            ->expects($this->at(1))
            ->method('getInternetMediaTypeStringFromFile')
            ->with('test')
            ->will($this->returnValue('test'));

        $adapter = $this->createAdapter();
        $adapter->setAdapters([$adapterMock1, $adapterMock2]);

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
