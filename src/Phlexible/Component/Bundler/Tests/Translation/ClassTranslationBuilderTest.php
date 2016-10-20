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

use Phlexible\Component\GuiAsset\Translation\ClassTranslationBuilder;

/**
 * @covers \Phlexible\Component\GuiAsset\Translation\ClassTranslationBuilder
 */
class ClassTranslationBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testExtract()
    {
        $builder = new ClassTranslationBuilder();
        $result = $builder->build(array('Phlexible.user.List' => array('title' => 'Users'), 'Phlexible.user.UserWindow' => array('userText' => 'User')), 'testDomain');

        $expected = <<<EOF
Ext.define("Ext.locale.testDomain.Phlexible.user.List", {
    "override": "Phlexible.user.List",
    "title": "Users"
});
Ext.define("Ext.locale.testDomain.Phlexible.user.UserWindow", {
    "override": "Phlexible.user.UserWindow",
    "userText": "User"
});

EOF;

        $this->assertEquals($expected, $result);
    }
}
