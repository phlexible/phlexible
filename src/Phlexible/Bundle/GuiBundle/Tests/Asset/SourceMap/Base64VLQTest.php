<?php

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\SourceMap;

use Phlexible\Bundle\GuiBundle\Asset\SourceMap\Base64VLQ;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\SourceMap\Base64VLQ
 */
class Base64VLQTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Base64VLQ
     */
    private $encoder;

    protected function setUp()
    {
        $this->encoder = new Base64VLQ();
    }

    public function testEncode()
    {
        $this->assertSame('A', $this->encoder->encode(0));
        $this->assertSame('C', $this->encoder->encode(1));
        $this->assertSame('K', $this->encoder->encode(5));
        $this->assertSame('Q', $this->encoder->encode(8));
        $this->assertSame('U', $this->encoder->encode(10));
        $this->assertSame('gB', $this->encoder->encode(16));
        $this->assertSame('oB', $this->encoder->encode(20));
        $this->assertSame('kD', $this->encoder->encode(50));
        $this->assertSame('oG', $this->encoder->encode(100));
    }

    public function testDecode()
    {
        $this->assertSame(0, $this->encoder->decode('A'));
        $this->assertSame(1, $this->encoder->decode('C'));
        $this->assertSame(5, $this->encoder->decode('K'));
        $this->assertSame(8, $this->encoder->decode('Q'));
        $this->assertSame(10, $this->encoder->decode('U'));
        $this->assertSame(16, $this->encoder->decode('gB'));
        $this->assertSame(20, $this->encoder->decode('oB'));
        $this->assertSame(50, $this->encoder->decode('kD'));
        $this->assertSame(100, $this->encoder->decode('oG'));
    }
}
