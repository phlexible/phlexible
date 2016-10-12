<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\ResourceResolver;

use Phlexible\Bundle\GuiBundle\Asset\ResourceResolver\ResolvedResources;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\ResourceResolver\ResolvedResources
 */
class ResolvedResourcesTest extends \PHPUnit_Framework_TestCase
{
    public function testResolvedResources()
    {
        $resolvedResources = new ResolvedResources(array('foo'), array('bar'));

        $this->assertSame(array('foo'), $resolvedResources->getResources());
        $this->assertSame(array('bar'), $resolvedResources->getUnusedResources());
    }
}
