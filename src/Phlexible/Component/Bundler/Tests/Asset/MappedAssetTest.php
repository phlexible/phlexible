<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Tests\Asset;

use Phlexible\Component\GuiAsset\Asset\MappedAsset;

/**
 * @covers \Phlexible\Component\GuiAsset\Asset\MappedAsset
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
