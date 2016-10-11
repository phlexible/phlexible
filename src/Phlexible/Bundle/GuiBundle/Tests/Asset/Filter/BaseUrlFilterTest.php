<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\Filter;

use Phlexible\Bundle\GuiBundle\Asset\Filter\BaseUrlFilter;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\Filter\BaseUrlFilter
 */
class BaseUrlFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterBasePath()
    {
        $filter = new BaseUrlFilter('bbb', 'aaa');

        $result = $filter->filter('/foo/BASE_PATH/bar');

        $expected = '/foo/aaa/bar';

        $this->assertSame($expected, $result);
    }

    public function testFilterBaseUrl()
    {
        $filter = new BaseUrlFilter('bbb', 'aaa');

        $result = $filter->filter('/bar/BASE_URL/baz');

        $expected = '/bar/bbb/baz';

        $this->assertSame($expected, $result);
    }

    public function testFilterBundlesPath()
    {
        $filter = new BaseUrlFilter('bbb', 'aaa');

        $result = $filter->filter('/baz/BUNDLES_PATH/bar');

        $expected = '/baz/aaa/bundles/bar';

        $this->assertSame($expected, $result);
    }
}
