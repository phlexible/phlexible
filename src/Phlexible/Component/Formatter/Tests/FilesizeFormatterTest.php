<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Formatter\Tests;

use Phlexible\Component\Formatter\FilesizeFormatter;

/**
 * Filesize formatter test
 */
class FilesizeFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilesizeFormatter
     */
    private $formatter;

    public function setUp()
    {
        $this->formatter = new FilesizeFormatter();
    }

    public function testZeroSize()
    {
        $this->assertEquals(0, $this->formatter->formatFilesize(0));
    }

    public function testWithZeroDecimals()
    {
        $this->assertEquals('1 Byte', $this->formatter->formatFilesize(1));
        $this->assertEquals('1 kB', $this->formatter->formatFilesize(pow(1000, 1)));
        $this->assertEquals('1 MB', $this->formatter->formatFilesize(pow(1000, 2)));
        $this->assertEquals('1 GB', $this->formatter->formatFilesize(pow(1000, 3)));
        $this->assertEquals('1 TB', $this->formatter->formatFilesize(pow(1000, 4)));
        $this->assertEquals('1 PB', $this->formatter->formatFilesize(pow(1000, 5)));
        $this->assertEquals('1000 PB', $this->formatter->formatFilesize(pow(1000, 6)));
    }

    public function testWithOneDecimal()
    {
        $this->assertEquals('1 Byte', $this->formatter->formatFilesize(1, 1));
        $this->assertEquals('2 Byte', $this->formatter->formatFilesize(1.5, 1));
        $this->assertEquals('1.5 kB', $this->formatter->formatFilesize(pow(1000, 1) * 1.5, 1));
        $this->assertEquals('1.5 MB', $this->formatter->formatFilesize(pow(1000, 2) * 1.5, 1));
        $this->assertEquals('1.5 GB', $this->formatter->formatFilesize(pow(1000, 3) * 1.5, 1));
        $this->assertEquals('1.5 TB', $this->formatter->formatFilesize(pow(1000, 4) * 1.5, 1));
        $this->assertEquals('1.5 PB', $this->formatter->formatFilesize(pow(1000, 5) * 1.5, 1));
        $this->assertEquals('1500.0 PB', $this->formatter->formatFilesize(pow(1000, 6) * 1.5, 1));
    }

    public function testWithTwoDecimals()
    {
        $this->assertEquals('1 Byte', $this->formatter->formatFilesize(1, 2));
        $this->assertEquals('2 Byte', $this->formatter->formatFilesize(1.5, 2));
        $this->assertEquals('1.50 kB', $this->formatter->formatFilesize(pow(1000, 1) * 1.5, 2));
        $this->assertEquals('1.50 MB', $this->formatter->formatFilesize(pow(1000, 2) * 1.5, 2));
        $this->assertEquals('1.50 GB', $this->formatter->formatFilesize(pow(1000, 3) * 1.5, 2));
        $this->assertEquals('1.50 TB', $this->formatter->formatFilesize(pow(1000, 4) * 1.5, 2));
        $this->assertEquals('1.50 PB', $this->formatter->formatFilesize(pow(1000, 5) * 1.5, 2));
        $this->assertEquals('1500.00 PB', $this->formatter->formatFilesize(pow(1000, 6) * 1.5, 2));
    }

    public function testBinaryWithZeroDecimals()
    {
        $this->assertEquals('1 Byte', $this->formatter->formatFilesize(1, 0, true));
        $this->assertEquals('1 KiB', $this->formatter->formatFilesize(pow(1024, 1), 0, true));
        $this->assertEquals('1 MiB', $this->formatter->formatFilesize(pow(1024, 2), 0, true));
        $this->assertEquals('1 GiB', $this->formatter->formatFilesize(pow(1024, 3), 0, true));
        $this->assertEquals('1 TiB', $this->formatter->formatFilesize(pow(1024, 4), 0, true));
        $this->assertEquals('1 PiB', $this->formatter->formatFilesize(pow(1024, 5), 0, true));
        $this->assertEquals('1024 PiB', $this->formatter->formatFilesize(pow(1024, 6), 0, true));
    }

    public function testBinaryWithOneDecimal()
    {
        $this->assertEquals('1 Byte', $this->formatter->formatFilesize(1, 1, true));
        $this->assertEquals('2 Byte', $this->formatter->formatFilesize(1.5, 1, true));
        $this->assertEquals('1.5 KiB', $this->formatter->formatFilesize(pow(1024, 1) * 1.5, 1, true));
        $this->assertEquals('1.5 MiB', $this->formatter->formatFilesize(pow(1024, 2) * 1.5, 1, true));
        $this->assertEquals('1.5 GiB', $this->formatter->formatFilesize(pow(1024, 3) * 1.5, 1, true));
        $this->assertEquals('1.5 TiB', $this->formatter->formatFilesize(pow(1024, 4) * 1.5, 1, true));
        $this->assertEquals('1.5 PiB', $this->formatter->formatFilesize(pow(1024, 5) * 1.5, 1, true));
        $this->assertEquals('1536.0 PiB', $this->formatter->formatFilesize(pow(1024, 6) * 1.5, 1, true));
    }

    public function testBinaryWithTwoDecimals()
    {
        $this->assertEquals('1 Byte', $this->formatter->formatFilesize(1, 2, true));
        $this->assertEquals('2 Byte', $this->formatter->formatFilesize(1.5, 2, true));
        $this->assertEquals('1.50 KiB', $this->formatter->formatFilesize(pow(1024, 1) * 1.5, 2, true));
        $this->assertEquals('1.50 MiB', $this->formatter->formatFilesize(pow(1024, 2) * 1.5, 2, true));
        $this->assertEquals('1.50 GiB', $this->formatter->formatFilesize(pow(1024, 3) * 1.5, 2, true));
        $this->assertEquals('1.50 TiB', $this->formatter->formatFilesize(pow(1024, 4) * 1.5, 2, true));
        $this->assertEquals('1.50 PiB', $this->formatter->formatFilesize(pow(1024, 5) * 1.5, 2, true));
        $this->assertEquals('1536.00 PiB', $this->formatter->formatFilesize(pow(1024, 6) * 1.5, 2, true));
    }

}
