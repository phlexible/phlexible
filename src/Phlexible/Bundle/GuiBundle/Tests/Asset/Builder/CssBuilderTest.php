<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\Builder;

use org\bovigo\vfs\vfsStream;
use Phlexible\Bundle\GuiBundle\Asset\Builder\CssBuilder;
use Phlexible\Bundle\GuiBundle\Asset\Finder\ResourceFinderInterface;
use Phlexible\Bundle\GuiBundle\Asset\MappedAsset;
use Phlexible\Bundle\GuiBundle\Asset\MappedContent\MappedContent;
use Phlexible\Bundle\GuiBundle\Asset\MappedContent\MappedContentBuilder;
use Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface;
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
