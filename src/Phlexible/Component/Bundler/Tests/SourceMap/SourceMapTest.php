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

/**
 * @covers \Phlexible\Component\GuiAsset\SourceMap\SourceMap
 */
class SourceMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return string
     */
    public function testToJson()
    {
        $map = new SourceMap('a', 'b', array('c'), array('d'), array('e'), 'f');

        $expected = json_encode(array(
            'version' => 3,
            'file' => 'a',
            'sourceRoot' => 'b',
            'sources' => array('c'),
            'sourcesContent' => array('d'),
            'names' => array('e'),
            'mappings' => 'f',
        ));

        $this->assertSame($expected, $map->toJson());
    }
}
