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

use Phlexible\Component\GuiAsset\SourceMap\Mapping;
use Phlexible\Component\GuiAsset\SourceMap\MappingEncoder;

/**
 * @covers \Phlexible\Component\GuiAsset\SourceMap\MappingEncoder
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
