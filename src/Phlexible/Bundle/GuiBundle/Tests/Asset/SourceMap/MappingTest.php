<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\SourceMap;

use Phlexible\Bundle\GuiBundle\Asset\SourceMap\Mapping;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\SourceMap\Mapping
 */
class MappingTest extends \PHPUnit_Framework_TestCase
{
    public function testMapping()
    {
        $mapping = new Mapping(1, 2, 3, 4, 5);

        $this->assertSame(1, $mapping->getDestLine());
        $this->assertSame(2, $mapping->getDestCol());
        $this->assertSame(3, $mapping->getSrcIndex());
        $this->assertSame(4, $mapping->getSrcLine());
        $this->assertSame(5, $mapping->getSrcCol());
    }
}
