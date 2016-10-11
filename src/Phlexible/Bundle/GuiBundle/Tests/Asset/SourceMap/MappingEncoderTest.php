<?php

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\SourceMap;

use Phlexible\Bundle\GuiBundle\Asset\SourceMap\Mapping;
use Phlexible\Bundle\GuiBundle\Asset\SourceMap\MappingEncoder;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\SourceMap\MappingEncoder
 */
class MappingEncoderTest extends \PHPUnit_Framework_TestCase
{
    public function testEncode()
    {
        $mappings = array(
            new Mapping(0, 0, 0, 0, 0),
            new Mapping(1, 0, 0, 1, 0),
            new Mapping(2, 0, 1, 0, 0),
        );

        $encoder = new MappingEncoder();
        $result = $encoder->encode($mappings);

        $this->assertSame('AAAA;AACA;ACDA;', $result);
    }
};
