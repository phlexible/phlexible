<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\SourceMap;

use Phlexible\Bundle\GuiBundle\Asset\SourceMap\Mapping;
use Phlexible\Bundle\GuiBundle\Asset\SourceMap\SourceMapIndex;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\SourceMap\SourceMapIndex
 */
class SourceMapIndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SourceMapIndex
     */
    private $index;

    protected function setUp()
    {
        $this->index = new SourceMapIndex('testSource', "line1\nline2\nline3");
    }

    public function testGetSource()
    {
        $this->assertSame('testSource', $this->index->getSource());
    }

    public function testGetContent()
    {
        $this->assertSame("line1\nline2\nline3", $this->index->getContent());
    }

    public function testGetMappings()
    {
        $mappings = $this->index->getMappings(5, 17);

        $expected = array(
            new Mapping(17, 0, 5, 0, 0),
            new Mapping(18, 0, 5, 1, 0),
            new Mapping(19, 0, 5, 2, 0),
        );

        $this->assertEquals($expected, $mappings);
    }
}
