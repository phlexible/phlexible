<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\Finder;

use org\bovigo\vfs\vfsStream;
use Phlexible\Bundle\GuiBundle\Asset\Finder\ResourceFinder;
use Puli\Discovery\Api\EditableDiscovery;
use Puli\Discovery\Binding\ResourceBinding;
use Puli\Repository\Resource\FileResource;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\Finder\ResourceFinder
 */
class ResourceFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testFindByType()
    {
        $root = vfsStream::setup();
        $testFile = vfsStream::newFile('test/file.txt')->at($root)->setContent('hello world!');

        $resource = new FileResource($testFile->url(), $testFile->path());

        $binding = $this->prophesize(ResourceBinding::class);
        $binding->getResources()->willReturn(array($resource));

        $discovery = $this->prophesize(EditableDiscovery::class);
        $discovery->findBindings('test/test')->willReturn(array($binding->reveal()));

        $finder = new ResourceFinder($discovery->reveal());

        $result = $finder->findByType('test/test');

        $expected = array(
            $testFile->path() => $resource,
        );

        $this->assertEquals($expected, $result);
    }
}
