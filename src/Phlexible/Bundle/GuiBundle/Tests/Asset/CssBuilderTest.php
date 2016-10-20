<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Tests\Builder;

use org\bovigo\vfs\vfsStream;
use Phlexible\Component\GuiAsset\Asset\MappedAsset;
use Phlexible\Component\GuiAsset\Builder\CssBuilder;
use Phlexible\Component\GuiAsset\Compressor\CompressorInterface;
use Phlexible\Component\GuiAsset\Finder\ResourceFinderInterface;
use Phlexible\Component\GuiAsset\MappedContent\MappedContent;
use Phlexible\Component\GuiAsset\MappedContent\MappedContentBuilder;
use Prophecy\Argument;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\Builder\CssBuilder
 */
class CssBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $root = vfsStream::setup();

        $finder = $this->prophesize(ResourceFinderInterface::class);
        $finder->findByType('phlexible/styles')->willReturn(array());

        $builder = $this->prophesize(MappedContentBuilder::class);
        $builder->build(Argument::cetera())->willReturn(new MappedContent('a', 'b'));

        $compressor = $this->prophesize(CompressorInterface::class);

        $builder = new CssBuilder($finder->reveal(), $builder->reveal(), $compressor->reveal(), $root->url(), false);

        $result = $builder->build('/a', '/b');

        $this->assertFileExists($root->getChild('gui.css')->url());
        $this->assertFileExists($root->getChild('gui.css.map')->url());

        $expected = new MappedAsset($root->getChild('gui.css')->url(), $root->getChild('gui.css.map')->url());
        $this->assertEquals($expected, $result);
    }
}
