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
use Phlexible\Component\Mime\Adapter\FallbackAdapter;

/**
 * Fallback adapter test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @covers \Phlexible\Component\Mime\Adapter\FallbackAdapter
 */
class FallbackAdapterTest extends AbstractAdapterTest
{
    public function testCheckAvailability()
    {
        $adapterMock = $this->prophesize(AdapterInterface::class);
        $adapterMock->isAvailable('testFile')->willReturn(true);

        $fallbackAdapterMock = $this->prophesize(AdapterInterface::class);
        $fallbackAdapterMock->isAvailable('testFile')->shouldNotBeCalled();

        $adapter = $this->createAdapter($adapterMock->reveal(), $fallbackAdapterMock->reveal());

        $this->assertTrue($adapter->isAvailable('testFile'));
    }

    public function testIsAvailableReturnsFalseOnNoneAvailable()
    {
        $adapterMock = $this->prophesize(AdapterInterface::class);
        $adapterMock->isAvailable('testFile')->willReturn(false);

        $fallbackAdapterMock = $this->prophesize(AdapterInterface::class);
        $fallbackAdapterMock->isAvailable('testFile')->willReturn(false);

        $adapter = $this->createAdapter($adapterMock->reveal(), $fallbackAdapterMock->reveal());

        $this->assertFalse($adapter->isAvailable('testFile'));
    }

    public function testIsAvailableReturnsTrueOnFallbackAdapterAvailable()
    {
        $adapterMock = $this->prophesize(AdapterInterface::class);
        $adapterMock->isAvailable('testFile')->willReturn(false);

        $fallbackAdapterMock = $this->prophesize(AdapterInterface::class);
        $fallbackAdapterMock->isAvailable('testFile')->willReturn(true);

        $adapter = $this->createAdapter($adapterMock->reveal(), $fallbackAdapterMock->reveal());

        $this->assertTrue($adapter->isAvailable('testFile'));
    }

    public function testIsAvailableReturnsFalseOnFallbackAdapterNotAvailable()
    {
        $adapterMock = $this->prophesize(AdapterInterface::class);
        $adapterMock->isAvailable('testFile')->willReturn(false);

        $fallbackAdapterMock = $this->prophesize(AdapterInterface::class);
        $fallbackAdapterMock->isAvailable('testFile')->willReturn(false);

        $adapter = $this->createAdapter($adapterMock->reveal(), $fallbackAdapterMock->reveal());

        $this->assertFalse($adapter->isAvailable('testFile'));
    }

    public function testGetMimetypeDoesNotUseFallbackAdapterOnValidAdapterResult()
    {
        $adapterMock = $this->prophesize(AdapterInterface::class);
        $adapterMock->isAvailable('test')->willReturn(true);
        $adapterMock->getInternetMediaTypeStringFromFile('test')->willReturn('image/jpg');

        $fallbackAdapterMock = $this->prophesize(AdapterInterface::class);
        $fallbackAdapterMock->isAvailable('test')->shouldNotBeCalled();
        $fallbackAdapterMock->getInternetMediaTypeStringFromFile('test')->shouldNotBeCalled();

        $adapter = $this->createAdapter($adapterMock->reveal(), $fallbackAdapterMock->reveal());

        $this->assertEquals('image/jpg', $adapter->getInternetMediaTypeStringFromFile('test'));
    }

    public function testGetMimetypeUsesFallbackAdapterOnEmptyAdapterResult()
    {
        $adapterMock = $this->prophesize(AdapterInterface::class);
        $adapterMock->isAvailable('test')->willReturn(true);
        $adapterMock->getInternetMediaTypeStringFromFile('test')->willReturn(true);

        $fallbackAdapterMock = $this->prophesize(AdapterInterface::class);
        $fallbackAdapterMock->isAvailable('test')->willReturn(true);
        $fallbackAdapterMock->getInternetMediaTypeStringFromFile('test')->willReturn('result');

        $adapter = $this->createAdapter($adapterMock->reveal(), $fallbackAdapterMock->reveal());

        $this->assertEquals('result', $adapter->getInternetMediaTypeStringFromFile('test'));
    }

    public function testGetMimetypeUsesFallbackAdapterOnFallbackResult()
    {
        $adapterMock = $this->prophesize(AdapterInterface::class);
        $adapterMock->isAvailable('test')->willReturn(true);
        $adapterMock->getInternetMediaTypeStringFromFile('test')->willReturn('application/octet-stream');

        $fallbackAdapterMock = $this->prophesize(AdapterInterface::class);
        $fallbackAdapterMock->isAvailable('test')->willReturn(true);
        $fallbackAdapterMock->getInternetMediaTypeStringFromFile('test')->willReturn('result');

        $adapter = $this->createAdapter($adapterMock->reveal(), $fallbackAdapterMock->reveal());

        $this->assertEquals('result', $adapter->getInternetMediaTypeStringFromFile('test'));
    }

    public function testGetMimetypeDoesNotUseFallbackAdapterOnUnavailableFallbackAdapter()
    {
        $adapterMock = $this->prophesize(AdapterInterface::class);
        $adapterMock->isAvailable('test')->willReturn(true);
        $adapterMock->getInternetMediaTypeStringFromFile('test')->willReturn('application/octet-stream');

        $fallbackAdapterMock = $this->prophesize(AdapterInterface::class);
        $fallbackAdapterMock->isAvailable('test')->willReturn(false);
        $fallbackAdapterMock->getInternetMediaTypeStringFromFile('test')->shouldNotBeCalled();

        $adapter = $this->createAdapter($adapterMock->reveal(), $fallbackAdapterMock->reveal());

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
