<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Tests\ResourceResolver;

use org\bovigo\vfs\vfsStream;
use Phlexible\Component\GuiAsset\ResourceResolver\ResolvedResources;
use Phlexible\Component\GuiAsset\ResourceResolver\RequireScriptResourceResolver;
use Puli\Repository\Resource\FileResource;

/**
 * @covers \Phlexible\Component\GuiAsset\ResourceResolver\RequireScriptResourceResolver
 */
class ScriptsResourceResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolveResources()
    {
        $root = vfsStream::setup('phlexible');
        $aFile = vfsStream::newFile('phlexiblegui/scripts/prototypes.js')->at($root)->setContent('console.log(123);');
        $bFile = vfsStream::newFile('phlexiblegui/scripts/functions.js')->at($root)->setContent('console.log(123);');
        $cFile = vfsStream::newFile('phlexiblegui/scripts/global.js')->at($root)->setContent('console.log(123);');

        $resolver = new RequireScriptResourceResolver();

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
