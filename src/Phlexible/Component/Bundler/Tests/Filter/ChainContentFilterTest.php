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

use Phlexible\Component\GuiAsset\Filter\ChainContentFilter;
use Phlexible\Component\GuiAsset\Filter\ContentFilterInterface;

/**
 * @covers \Phlexible\Component\GuiAsset\Filter\ChainContentFilter
 */
class ChainContentFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterBasePath()
    {
        $filter1 = $this->prophesize(ContentFilterInterface::class);
        $filter1->filter('foo')->shouldBeCalled()->willReturn('bar');
        $filter2 = $this->prophesize(ContentFilterInterface::class);
        $filter2->filter('bar')->shouldBeCalled()->willReturn('baz');

        $filter = new ChainContentFilter(array($filter1->reveal(), $filter2->reveal()));

        $result = $filter->filter('foo');

        $this->assertSame('baz', $result);
    }
}
