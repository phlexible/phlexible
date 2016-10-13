<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\SourceMap;

use Phlexible\Bundle\GuiBundle\Asset\SourceMap\SourceMap;
use Phlexible\Bundle\GuiBundle\Asset\SourceMap\SourceMapBuilder;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\SourceMap\SourceMapBuilder
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
