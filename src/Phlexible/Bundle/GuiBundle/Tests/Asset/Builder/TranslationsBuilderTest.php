<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\Builder;

use org\bovigo\vfs\vfsStream;
use Phlexible\Bundle\GuiBundle\Asset\Builder\TranslationsBuilder;
use Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface;
use Prophecy\Argument;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\TranslatorBagInterface;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\Builder\TranslationsBuilder
 */
class TranslationsBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $root = vfsStream::setup();

        $translator = $this->prophesize(TranslatorBagInterface::class);
        $translator->getCatalogue('de')->willReturn(new MessageCatalogue('de', array('testDomain' => array('Foo.bar.Baz' => 'foobar', 'Me.here.There' => 'hello world'))));

        $compressor = $this->prophesize(CompressorInterface::class);
        $compressor->compressString(Argument::type('string'))->willReturnArgument(0);

        $builder = new TranslationsBuilder($translator->reveal(), $compressor->reveal(), $root->url());

        $result = $builder->build('de', 'testDomain');

        $expected = <<<EOF
Ext.namespace("Phlexible.foo");
Phlexible.foo.Strings = {"bar":{"Baz":"foobar"}};
Phlexible.foo.Strings.get = function(s){return this[s]};
Ext.namespace("Phlexible.me");
Phlexible.me.Strings = {"here":{"There":"hello world"}};
Phlexible.me.Strings.get = function(s){return this[s]};

EOF;

        $this->assertFileExists($root->getChild('translations-de.js')->url());
        $this->assertSame($root->getChild('translations-de.js')->url(), $result);
        $this->assertSame($expected, file_get_contents($root->getChild('translations-de.js')->url()));
    }
}
