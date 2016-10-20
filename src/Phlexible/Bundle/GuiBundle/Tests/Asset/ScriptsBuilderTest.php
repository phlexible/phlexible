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
use Phlexible\Bundle\GuiBundle\Asset\ScriptsBuilder;
use Phlexible\Component\GuiAsset\Asset\MappedAsset;
use Phlexible\Component\GuiAsset\Compressor\CompressorInterface;
use Phlexible\Component\GuiAsset\Content\MappedContent;
use Phlexible\Component\GuiAsset\ContentBuilder\MappedContentBuilder;
use Phlexible\Component\GuiAsset\Finder\ResourceFinderInterface;
use Phlexible\Component\GuiAsset\ResourceResolver\ResolvedResources;
use Phlexible\Component\GuiAsset\ResourceResolver\ResourceResolverInterface;
use Prophecy\Argument;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\ScriptsBuilder
 */
class ScriptsBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $root = vfsStream::setup('phlexible');

        $finder = $this->prophesize(ResourceFinderInterface::class);
        $finder->findByType('phlexible/scripts')->willReturn(array());

        $resolver = $this->prophesize(ResourceResolverInterface::class);
        $resolver->resolve(array())->willReturn(new ResolvedResources(array(), array()));

        $builder = $this->prophesize(MappedContentBuilder::class);
        $builder->build(Argument::cetera())->willReturn(new MappedContent('a', 'b'));

        $compressor = $this->prophesize(CompressorInterface::class);

        $builder = new ScriptsBuilder(
            $finder->reveal(),
            $resolver->reveal(),
            $builder->reveal(),
            $compressor->reveal(),
            $root->url(),
            false
        );

        $result = $builder->build();

        $this->assertFileExists($root->getChild('gui.js')->url());
        $this->assertFileExists($root->getChild('gui.js.map')->url());

        $expected = new MappedAsset($root->getChild('gui.js')->url(), $root->getChild('gui.js.map')->url());
        $this->assertEquals($expected, $result);
    }
}
