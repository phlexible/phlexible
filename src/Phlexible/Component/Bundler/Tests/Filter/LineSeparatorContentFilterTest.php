<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Tests\Filter;

use Phlexible\Component\GuiAsset\Filter\LineSeparatorContentFilter;

/**
 * @covers \Phlexible\Component\GuiAsset\Filter\LineSeparatorContentFilter
 */
class LineSeparatorContentFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterToLineFeed()
    {
        $filter = new LineSeparatorContentFilter("\n");

        $input = "hello\rworld\nhow\r\nare\ryou\r\ntoday\n?";
        $expected = "hello\nworld\nhow\nare\nyou\ntoday\n?";

        $result = $filter->filter($input);

        $this->assertSame($expected, $result);
    }

    public function testFilterToCarriageReturn()
    {
        $filter = new LineSeparatorContentFilter("\r");

        $input = "hello\rworld\nhow\r\nare\ryou\r\ntoday\n?";
        $expected = "hello\rworld\rhow\rare\ryou\rtoday\r?";

        $result = $filter->filter($input);

        $this->assertSame($expected, $result);
    }

    public function testFilterToCarriageReturnLineFeed()
    {
        $filter = new LineSeparatorContentFilter("\r\n");

        $input = "hello\rworld\nhow\r\nare\ryou\r\ntoday\n?";
        $expected = "hello\r\nworld\r\nhow\r\nare\r\nyou\r\ntoday\r\n?";

        $result = $filter->filter($input);

        $this->assertSame($expected, $result);
    }
}
