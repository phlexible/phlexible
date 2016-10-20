<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Tests\SourceMap;

use Phlexible\Component\GuiAsset\SourceMap\SourceMap;
use Phlexible\Component\GuiAsset\SourceMap\SourceMapBuilder;

/**
 * @covers \Phlexible\Component\GuiAsset\SourceMap\SourceMapBuilder
 */
class SourceMapBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSourceMap()
    {
        $builder = new SourceMapBuilder('testFile', 0);
        $builder->add('testSource1', "line1\nline2\nline3");
        $builder->add('testSource2', "line4\nline5\nline6");
        $map = $builder->getSourceMap();

        $expected = new SourceMap(
            'testFile',
            '',
            array('testSource1', 'testSource2'),
            array("line1\nline2\nline3", "line4\nline5\nline6"),
            array(),
            'AAAA;AACA;AACA;ACFA;AACA;AACA;'
        );

        $this->assertEquals($expected, $map);
    }
}
