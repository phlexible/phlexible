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

use Phlexible\Component\GuiAsset\Filter\EnsureTrailingSeparatorContentFilter;

/**
 * @covers \Phlexible\Component\GuiAsset\Filter\EnsureTrailingSeparatorContentFilter
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
