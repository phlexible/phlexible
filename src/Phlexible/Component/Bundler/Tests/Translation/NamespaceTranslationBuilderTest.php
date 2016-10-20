<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Tests\Translation;

use Phlexible\Component\GuiAsset\Translation\NamespaceTranslationBuilder;

/**
 * @covers \Phlexible\Component\GuiAsset\Translation\NamespaceTranslationBuilder
 */
class NamespaceTranslationBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $builder = new NamespaceTranslationBuilder();
        $result = $builder->build(array('foo' => array('bar' => 'Baz'), 'hello' => array('one' => 'World', 'two' => array('three' => 'Again'))), 'de');

        $expected = <<<EOF
Ext.namespace("Phlexible.foo");
Phlexible.foo.Strings = {"bar":"Baz"};
Phlexible.foo.Strings.get = function(s){return this[s]};
Ext.namespace("Phlexible.hello");
Phlexible.hello.Strings = {"one":"World","two":{"three":"Again"}};
Phlexible.hello.Strings.get = function(s){return this[s]};

EOF;

        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException \Phlexible\Component\GuiAsset\Exception\InvalidArgumentException
     */
    public function testBuildThrowsExceptionForNamespacesWithDots()
    {
        $builder = new NamespaceTranslationBuilder();
        $builder->build(array('Foo.bar.Baz' => 'foobar'), 'de');
    }

    /**
     * @expectedException \Phlexible\Component\GuiAsset\Exception\InvalidArgumentException
     */
    public function testBuildThrowsExceptionForPagesThatAreNotArrays()
    {
        $builder = new NamespaceTranslationBuilder();
        $builder->build(array('foo' => 'bar'), 'de');
    }
}
