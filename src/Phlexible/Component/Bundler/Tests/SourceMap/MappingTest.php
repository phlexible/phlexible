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

/**
 * @covers \Phlexible\Component\GuiAsset\SourceMap\Mapping
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDestLineThrowsExceptions()
    {
        new Mapping(-1, 2, 3, 4, 5);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDestColThrowsExceptions()
    {
        new Mapping(1, -1, 3, 4, 5);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidSrcIndexThrowsExceptions()
    {
        new Mapping(1, 2, -1, 4, 5);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidSrcLineThrowsExceptions()
    {
        new Mapping(1, 2, 3, -1, 5);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidSrcColThrowsExceptions()
    {
        new Mapping(1, 2, 3, 4, -1);
    }
}
