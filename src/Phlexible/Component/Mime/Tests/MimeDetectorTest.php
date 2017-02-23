<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Mime\Tests;

use Phlexible\Component\Mime\Adapter\AdapterInterface;
use Phlexible\Component\Mime\MimeDetector;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @author Stephan Wentz <swentz@brainbits.net>
 *
 * @covers \Phlexible\Component\Mime\MimeDetector
 */
class MimeDetectorTest extends TestCase
{
    /**
     * @var MimeDetector
     */
    private $detector;

    /**
     * @var AdapterInterface|ObjectProphecy
     */
    private $adapter;

    public function setUp()
    {
        $this->adapter = $this->prophesize(AdapterInterface::class);
        $this->detector = new MimeDetector($this->adapter->reveal());
    }

    public function tearDown()
    {
        $this->detector = null;
    }

    public function testGetMimetypeReturnsNullOnEmptyAdapter()
    {
        $detector = new MimeDetector();
        $mimetype = $detector->detect('dummyFile');

        $this->assertNull($mimetype);
    }

    public function testGetAdapterReturnsNullOnEmptyAdapter()
    {
        $detector = new MimeDetector();
        $adapter = $detector->getAdapter();

        $this->assertNull($adapter);
    }

    public function testConstructorInjection()
    {
        $this->assertSame($this->adapter->reveal(), $this->detector->getAdapter());
    }

    public function testGetMimetypeCallsAdapter()
    {
        $this->adapter->isAvailable('dummyFile')->willReturn(true);
        $this->adapter->getInternetMediaTypeStringFromFile('dummyFile')->willReturn('image/jpeg');

        $this->detector->detect('dummyFile');
    }

    public function testGetMimetypeReturnsNullOnUnavailableAdapter()
    {
        $this->adapter->isAvailable('dummyFile')->willReturn(false);

        $this->detector->detect('dummyFile');
    }

    public function testGetMimetypeReturnsNullOnAdapterException()
    {
        $this->adapter->isAvailable('dummyFile')->willReturn(false);
        $this->adapter->getInternetMediaTypeStringFromFile('dummyFile')->willThrow(new \Exception('test'));

        $internetMediaType = $this->detector->detect('dummyFile');

        $this->assertNull($internetMediaType);
    }

    public function testInternetMediaTypeHasCorrectValue()
    {
        $this->adapter->isAvailable('dummyFile')->willReturn(true);
        $this->adapter->getInternetMediaTypeStringFromFile('dummyFile')->willReturn('plain/text; charset=utf8');

        $internetMediaType = $this->detector->detect('dummyFile', MimeDetector::RETURN_STRING);

        $this->assertEquals('plain/text; charset=utf8', (string) $internetMediaType);
    }
}
