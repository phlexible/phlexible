<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Tests\ResourceResolver;

use Phlexible\Component\GuiAsset\ResourceResolver\ResolvedResources;

/**
 * @covers \Phlexible\Component\GuiAsset\ResourceResolver\ResolvedResources
 */
class ResolvedResourcesTest extends \PHPUnit_Framework_TestCase
{
    public function testResolvedResources()
    {
        $resolvedResources = new ResolvedResources(array('foo'), array('bar'));

        $this->assertSame(array('foo'), $resolvedResources->getResources());
        $this->assertSame(array('bar'), $resolvedResources->getUnusedResources());
    }
}
