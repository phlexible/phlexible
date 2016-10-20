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

use Phlexible\Component\GuiAsset\Translation\ClassTranslationExtractor;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * @covers \Phlexible\Component\GuiAsset\Translation\ClassTranslationExtractor
 */
class ClassTranslationExtractorTest extends \PHPUnit_Framework_TestCase
{
    public function testExtract()
    {
        $messageCatalog = new MessageCatalogue('de', array('testDomain' => array('Phlexible.user.MainView.title' => 'Users', 'Phlexible.user.UserWindow.user' => 'User')));

        $extractor = new ClassTranslationExtractor();
        $result = $extractor->extract($messageCatalog, 'testDomain');

        $expected = array(
            'Phlexible.user.MainView' => array('title' => 'Users'),
            'Phlexible.user.UserWindow' => array('user' => 'User'),
        );

        $this->assertEquals($expected, $result);
    }
}
