<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\Filter;

use Phlexible\Bundle\GuiBundle\Asset\Filter\BaseUrlContentFilter;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\Filter\BaseUrlContentFilter
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
