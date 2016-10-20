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

use Phlexible\Component\GuiAsset\Filter\BaseUrlContentFilter;

/**
 * @covers \Phlexible\Component\GuiAsset\Filter\BaseUrlContentFilter
 */
class BaseUrlContentFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterBasePath()
    {
        $filter = new BaseUrlContentFilter('/app.php', '/');

        $result = $filter->filter('/BASE_PATH/foo.txt');

        $expected = '/foo.txt';

        $this->assertSame($expected, $result);
    }

    public function testFilterBaseUrl()
    {
        $filter = new BaseUrlContentFilter('/app.php', '/');

        $result = $filter->filter('/BASE_URL/bar.txt');

        $expected = '/app.php/bar.txt';

        $this->assertSame($expected, $result);
    }

    public function testFilterBundlesPath()
    {
        $filter = new BaseUrlContentFilter('/app.php', '/');

        $result = $filter->filter('/BUNDLES_PATH/baz.txt');

        $expected = '/bundles/baz.txt';

        $this->assertSame($expected, $result);
    }
}
