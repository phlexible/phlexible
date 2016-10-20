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

use Phlexible\Component\GuiAsset\Asset\Asset;

/**
 * @covers \Phlexible\Component\GuiAsset\Asset\Asset
 */
class AssetTest extends \PHPUnit_Framework_TestCase
{
    public function testAsset()
    {
        $asset = new Asset('foo');

        $this->assertSame('foo', $asset->getFile());
    }
}
