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
use Phlexible\Component\Bundler\Asset\MappedAsset;
use Phlexible\Component\Bundler\Compressor\CompressorInterface;
use Phlexible\Component\Bundler\Content\MappedContent;
use Phlexible\Component\Bundler\ContentBuilder\MappedContentBuilder;
use Phlexible\Component\Bundler\Finder\ResourceFinderInterface;
use Phlexible\Component\Bundler\ResourceResolver\ResolvedResources;
use Phlexible\Component\Bundler\ResourceResolver\ResourceResolverInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * Scripts builder test.
 *
 * @covers \Phlexible\Bundle\GuiBundle\Asset\ScriptsBuilder
 */
class ScriptsBuilderTest extends TestCase
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
