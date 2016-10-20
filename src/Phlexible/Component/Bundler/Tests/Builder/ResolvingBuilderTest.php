<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Tests\Asset\Builder;

use org\bovigo\vfs\vfsStream;
use Phlexible\Component\GuiAsset\Asset\MappedAsset;
use Phlexible\Component\GuiAsset\Builder\ResolvingBuilder;
use Phlexible\Component\GuiAsset\Compressor\CompressorInterface;
use Phlexible\Component\GuiAsset\Content\MappedContent;
use Phlexible\Component\GuiAsset\ContentBuilder\MappedContentBuilder;
use Phlexible\Component\GuiAsset\Finder\ResourceFinderInterface;
use Phlexible\Component\GuiAsset\ResourceResolver\ResolvedResources;
use Phlexible\Component\GuiAsset\ResourceResolver\ResourceResolverInterface;
use Prophecy\Argument;

class TestBuilder extends ResolvingBuilder
{
    protected function getFilename()
    {
        return 'test.txt';
    }

    protected function getType()
    {
        return 'test/test';
    }

    protected function sanitizePath($path)
    {
        return $path;
    }

    protected function prefixContent(ResolvedResources $resources)
    {
        return '';
    }
}
/**
 * @covers \Phlexible\Component\GuiAsset\Builder\ResolvingBuilder
 */
class ResolvingBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $root = vfsStream::setup('phlexible');

        $finder = $this->prophesize(ResourceFinderInterface::class);
        $finder->findByType('test/test')->willReturn(array());

        $resolver = $this->prophesize(ResourceResolverInterface::class);
        $resolver->resolve(array())->willReturn(new ResolvedResources(array(), array()));

        $builder = $this->prophesize(MappedContentBuilder::class);
        $builder->build(Argument::cetera())->willReturn(new MappedContent('a', 'b'));

        $compressor = $this->prophesize(CompressorInterface::class);

        $builder = new TestBuilder(
            $finder->reveal(),
            $resolver->reveal(),
            $builder->reveal(),
            $compressor->reveal(),
            $root->url(),
            false
        );

        $result = $builder->build();

        $this->assertFileExists($root->getChild('test.txt')->url());
        $this->assertFileExists($root->getChild('test.txt.map')->url());

        $expected = new MappedAsset($root->getChild('test.txt')->url(), $root->getChild('test.txt.map')->url());
        $this->assertEquals($expected, $result);
    }
}
