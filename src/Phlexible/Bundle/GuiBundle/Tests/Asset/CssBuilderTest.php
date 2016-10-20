<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset;

use org\bovigo\vfs\vfsStream;
use Phlexible\Bundle\GuiBundle\Asset\CssBuilder;
use Phlexible\Component\GuiAsset\Asset\MappedAsset;
use Phlexible\Component\GuiAsset\Compressor\CompressorInterface;
use Phlexible\Component\GuiAsset\Content\MappedContent;
use Phlexible\Component\GuiAsset\ContentBuilder\MappedContentBuilder;
use Phlexible\Component\GuiAsset\Finder\ResourceFinderInterface;
use Phlexible\Component\GuiAsset\ResourceResolver\ResolvedResources;
use Phlexible\Component\GuiAsset\ResourceResolver\ResourceResolverInterface;
use Prophecy\Argument;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\CssBuilder
 */
class CssBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $root = vfsStream::setup();

        $resourceFinder = $this->prophesize(ResourceFinderInterface::class);
        $resourceFinder->findByType('phlexible/styles')->willReturn(array());

        $resourceResolver = $this->prophesize(ResourceResolverInterface::class);
        $resourceResolver->resolve(array())->willReturn(new ResolvedResources(array()));

        $contentBuilder = $this->prophesize(MappedContentBuilder::class);
        $contentBuilder->build(Argument::cetera())->willReturn(new MappedContent('a', 'b'));

        $compressor = $this->prophesize(CompressorInterface::class);

        $builder = new CssBuilder($resourceFinder->reveal(), $resourceResolver->reveal(), $contentBuilder->reveal(), $compressor->reveal(), $root->url(), false);

        $result = $builder->build('/a', '/b');

        $this->assertFileExists($root->getChild('gui.css')->url());
        $this->assertFileExists($root->getChild('gui.css.map')->url());

        $expected = new MappedAsset($root->getChild('gui.css')->url(), $root->getChild('gui.css.map')->url());
        $this->assertEquals($expected, $result);
    }
}
