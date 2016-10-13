<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\Filter;

use Phlexible\Bundle\GuiBundle\Asset\Filter\ChainContentFilter;
use Phlexible\Bundle\GuiBundle\Asset\Filter\ContentFilterInterface;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\Filter\ChainContentFilter
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
