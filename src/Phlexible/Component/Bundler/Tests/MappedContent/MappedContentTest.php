<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Tests\MappedContent;

use Phlexible\Component\GuiAsset\Content\MappedContent;

/**
 * @covers \Phlexible\Component\GuiAsset\MappedContent\MappedContent
 */
class MappedContentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return string
     */
    public function testMappedContent()
    {
        $mappedContent = new MappedContent('foo', 'bar');

        $this->assertSame('foo', $mappedContent->getContent());
        $this->assertSame('bar', $mappedContent->getMap());
    }
}
