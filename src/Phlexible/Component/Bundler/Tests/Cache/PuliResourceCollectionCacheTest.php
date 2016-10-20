<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Tests\Asset\Cache;

use org\bovigo\vfs\vfsStream;
use Phlexible\Component\GuiAsset\Cache\PuliResourceCollectionCache;
use Puli\Repository\Resource\FileResource;

/**
 * @covers \Phlexible\Component\GuiAsset\Cache\PuliResourceCollectionCache
 */
class PuliResourceCollectionCacheTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFreshReturnsFalseForMissingFile()
    {
        vfsStream::setup();

        $cache = new PuliResourceCollectionCache(vfsStream::url('invalid/file'), false);
        $result = $cache->isFresh(array());
        $this->assertFalse($result);
    }

    public function testIsFreshReturnsTrueForEmptyResources()
    {
        $root = vfsStream::setup();
        $cacheFile = vfsStream::newFile('cache')->at($root)->setContent('test');

        $cache = new PuliResourceCollectionCache($cacheFile->url(), false);
        $result = $cache->isFresh(array());
        $this->assertTrue($result);
    }

    public function testIsFreshReturnsFalseForOutdatedResources()
    {
        $root = vfsStream::setup();
        $cacheFile = vfsStream::newFile('cache')->at($root)->setContent('cache')->lastModified(time() - 100);
        $resourceFile = vfsStream::newFile('resource')->at($root)->setContent('resource');

        $cache = new PuliResourceCollectionCache($cacheFile->url(), false);
        $result = $cache->isFresh(array(new FileResource($resourceFile->url(), $resourceFile->path())));

        $this->assertFalse($result);
    }

    public function testIsFreshReturnsTrueForResources()
    {
        $root = vfsStream::setup();
        $cacheFile = vfsStream::newFile('cache')->at($root)->setContent('cache');
        $resourceFile = vfsStream::newFile('resource')->at($root)->setContent('resource');

        $cache = new PuliResourceCollectionCache($cacheFile->url(), false);
        $result = $cache->isFresh(array(new FileResource($resourceFile->url(), $resourceFile->path())));

        $this->assertTrue($result);
    }

    public function testWrite()
    {
        $root = vfsStream::setup();
        $cacheFile = vfsStream::newFile('cache')->at($root);

        $cache = new PuliResourceCollectionCache($cacheFile->url(), false);
        $cache->write('testContent');

        $this->assertSame('testContent', file_get_contents($cacheFile->url()));
    }
}
