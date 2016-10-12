<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Mime\Tests\Adapter;

use Phlexible\Component\Mime\Adapter\FallbackAdapter;

/**
 * Fallback adapter test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FallbackAdapterTest extends AbstractAdapterTest
{
    public function testCheckAvailability()
    {
        $adapterMock = $this->createAdapterMock();
        $adapterMock
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(true));

        $fallbackAdapterMock = $this->createAdapterMock();
        $fallbackAdapterMock
            ->expects($this->never())
            ->method('isAvailable');

        $adapter = $this->createAdapter($adapterMock, $fallbackAdapterMock);

        $this->assertTrue($adapter->isAvailable('testFile'));
    }

    public function testIsAvailableReturnsFalseOnNoneAvailable()
    {
        $adapterMock = $this->createAdapterMock();
        $adapterMock
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(false));

        $fallbackAdapterMock = $this->createAdapterMock();
        $fallbackAdapterMock
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(false));

        $adapter = $this->createAdapter($adapterMock, $fallbackAdapterMock);

        $this->assertFalse($adapter->isAvailable('testFile'));
    }

    public function testIsAvailableReturnsTrueOnFallbackAdapterAvailable()
    {
        $adapterMock = $this->createAdapterMock();
        $adapterMock
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(false));

        $fallbackAdapterMock = $this->createAdapterMock();
        $fallbackAdapterMock
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(true));

        $adapter = $this->createAdapter($adapterMock, $fallbackAdapterMock);

        $this->assertTrue($adapter->isAvailable('testFile'));
    }

    public function testIsAvailableReturnsFalseOnFallbackAdapterNotAvailable()
    {
        $adapterMock = $this->createAdapterMock();
        $adapterMock
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(false));

        $fallbackAdapterMock = $this->createAdapterMock();
        $fallbackAdapterMock
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(false));

        $adapter = $this->createAdapter($adapterMock, $fallbackAdapterMock);

        $this->assertFalse($adapter->isAvailable('testFile'));
    }

    public function testGetMimetypeDoesNotUseFallbackAdapterOnValidAdapterResult()
    {
        $adapterMock = $this->createAdapterMock();
        $adapterMock
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $adapterMock
            ->expects($this->at(1))
            ->method('getInternetMediaTypeStringFromFile')
            ->will($this->returnValue('image/jpg'));

        $fallbackAdapterMock = $this->createAdapterMock();
        $fallbackAdapterMock
            ->expects($this->never())
            ->method('isAvailable');
        $fallbackAdapterMock
            ->expects($this->never())
            ->method('getInternetMediaTypeStringFromFile');

        $adapter = $this->createAdapter($adapterMock, $fallbackAdapterMock);

        $this->assertEquals('image/jpg', $adapter->getInternetMediaTypeStringFromFile('test'));
    }

    public function testGetMimetypeUsesFallbackAdapterOnEmptyAdapterResult()
    {
        $adapterMock = $this->createAdapterMock();
        $adapterMock
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $adapterMock
            ->expects($this->at(1))
            ->method('getInternetMediaTypeStringFromFile')
            ->will($this->returnValue(true));

        $fallbackAdapterMock = $this->createAdapterMock();
        $fallbackAdapterMock
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $fallbackAdapterMock
            ->expects($this->at(1))
            ->method('getInternetMediaTypeStringFromFile')
            ->will($this->returnValue('result'));

        $adapter = $this->createAdapter($adapterMock, $fallbackAdapterMock);

        $this->assertEquals('result', $adapter->getInternetMediaTypeStringFromFile('test'));
    }

    public function testGetMimetypeUsesFallbackAdapterOnFallbackResult()
    {
        $adapterMock = $this->createAdapterMock();
        $adapterMock
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $adapterMock
            ->expects($this->at(1))
            ->method('getInternetMediaTypeStringFromFile')
            ->will($this->returnValue('application/octet-stream'));

        $fallbackAdapterMock = $this->createAdapterMock();
        $fallbackAdapterMock
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $fallbackAdapterMock
            ->expects($this->at(1))
            ->method('getInternetMediaTypeStringFromFile')
            ->will($this->returnValue('result'));

        $adapter = $this->createAdapter($adapterMock, $fallbackAdapterMock);

        $this->assertEquals('result', $adapter->getInternetMediaTypeStringFromFile('test'));
    }

    public function testGetMimetypeDoesNotUseFallbackAdapterOnUnavailableFallbackAdapter()
    {
        $adapterMock = $this->createAdapterMock();
        $adapterMock
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(true));
        $adapterMock
            ->expects($this->at(1))
            ->method('getInternetMediaTypeStringFromFile')
            ->will($this->returnValue('application/octet-stream'));

        $fallbackAdapterMock = $this->createAdapterMock();
        $fallbackAdapterMock
            ->expects($this->at(0))
            ->method('isAvailable')
            ->will($this->returnValue(false));

        $adapter = $this->createAdapter($adapterMock, $fallbackAdapterMock);

        $this->assertEquals('application/octet-stream', $adapter->getInternetMediaTypeStringFromFile('test'));
    }

    /**
     * {@inheritdoc}
     */
    protected function createAdapter($adapterMock = null, $fallbackAdapterMock = null)
    {
        return new FallbackAdapter($adapterMock, $fallbackAdapterMock);
    }
}
