<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\ResourceResolver;

use org\bovigo\vfs\vfsStream;
use Phlexible\Bundle\GuiBundle\Asset\ResourceResolver\ResolvedResources;
use Phlexible\Bundle\GuiBundle\Asset\ResourceResolver\ScriptsResourceResolver;
use Puli\Repository\Resource\FileResource;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\ResourceResolver\ScriptsResourceResolver
 */
class ScriptsResourceResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolveResources()
    {
        $root = vfsStream::setup('phlexible');
        $aFile = vfsStream::newFile('phlexiblegui/scripts/prototypes.js')->at($root)->setContent('console.log(123);');
        $bFile = vfsStream::newFile('phlexiblegui/scripts/functions.js')->at($root)->setContent('console.log(123);');
        $cFile = vfsStream::newFile('phlexiblegui/scripts/global.js')->at($root)->setContent('console.log(123);');

        $resolver = new ScriptsResourceResolver();

        $resources = array(
            new FileResource($aFile->url(), '/'.$aFile->path()),
            new FileResource($bFile->url(), '/'.$bFile->path()),
            new FileResource($cFile->url(), '/'.$cFile->path()),
        );

        $result = $resolver->resolve($resources);

        $expected = new ResolvedResources(
            $resources,
            array()
        );

        $this->assertEquals($expected, $result);
    }
}
