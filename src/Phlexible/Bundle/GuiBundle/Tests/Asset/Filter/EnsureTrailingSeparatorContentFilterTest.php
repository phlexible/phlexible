<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\Filter;

use Phlexible\Bundle\GuiBundle\Asset\Filter\EnsureTrailingSeparatorContentFilter;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\Filter\EnsureTrailingSeparatorContentFilter
 */
class EnsureTrailingSeparatorContentFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new EnsureTrailingSeparatorContentFilter("\n");

        $this->assertSame("hello world!\n", $filter->filter("hello world!"));
        $this->assertSame("hello world!\n", $filter->filter("hello world!\n"));
    }
}
