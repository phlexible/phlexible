<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset;

use Phlexible\Bundle\GuiBundle\Asset\Asset;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\Asset
 */
class AssetTest extends \PHPUnit_Framework_TestCase
{
    public function testAsset()
    {
        $asset = new Asset('foo');

        $this->assertSame('foo', $asset->getFile());
    }
}
