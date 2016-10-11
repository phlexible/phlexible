<?php

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\SourceMap;

use Phlexible\Bundle\GuiBundle\Asset\SourceMap\SourceMap;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\SourceMap\SourceMap
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
