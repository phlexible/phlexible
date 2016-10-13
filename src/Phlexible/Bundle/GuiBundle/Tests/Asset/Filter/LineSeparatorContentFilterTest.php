<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\Filter;

use Phlexible\Bundle\GuiBundle\Asset\Filter\LineSeparatorContentFilter;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\Filter\LineSeparatorContentFilter
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
