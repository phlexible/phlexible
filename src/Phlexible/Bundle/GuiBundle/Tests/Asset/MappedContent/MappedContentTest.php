<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\MappedContent;

use Phlexible\Bundle\GuiBundle\Asset\MappedContent\MappedContent;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\MappedContent\MappedContent
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
