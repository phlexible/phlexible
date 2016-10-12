<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset;

use Phlexible\Bundle\GuiBundle\Asset\MappedAsset;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\MappedAsset
 */
class MappedAssetTest extends \PHPUnit_Framework_TestCase
{
    public function testAsset()
    {
        $asset = new MappedAsset('foo', 'bar');

        $this->assertSame('foo', $asset->getFile());
        $this->assertSame('bar', $asset->getMapFile());
    }
}
