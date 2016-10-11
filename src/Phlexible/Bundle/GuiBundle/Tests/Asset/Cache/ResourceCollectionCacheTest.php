<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\Cache;

use org\bovigo\vfs\vfsStream;
use Phlexible\Bundle\GuiBundle\Asset\Cache\ResourceCollectionCache;
use Puli\Repository\Resource\FileResource;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\Cache\ResourceCollectionCache
 */
class ResourceCollectionCacheTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFreshReturnsFalseForMissingFile()
    {
        vfsStream::setup();

        $cache = new ResourceCollectionCache(vfsStream::url('invalid/file'), false);
        $result = $cache->isFresh(array());
        $this->assertFalse($result);
    }

    public function testIsFreshReturnsTrueForEmptyResources()
    {
        $root = vfsStream::setup();
        $cacheFile = vfsStream::newFile('cache')->at($root)->setContent('test');

        $cache = new ResourceCollectionCache($cacheFile->url(), false);
        $result = $cache->isFresh(array());
        $this->assertTrue($result);
    }

    public function testIsFreshReturnsFalseForOutdatedResources()
    {
        $root = vfsStream::setup();
        $cacheFile = vfsStream::newFile('cache')->at($root)->setContent('cache')->lastModified(time() - 100);
        $resourceFile = vfsStream::newFile('resource')->at($root)->setContent('resource');

        $cache = new ResourceCollectionCache($cacheFile->url(), false);
        $result = $cache->isFresh(array(new FileResource($resourceFile->url(), $resourceFile->path())));

        $this->assertFalse($result);
    }

    public function testIsFreshReturnsTrueForResources()
    {
        $root = vfsStream::setup();
        $cacheFile = vfsStream::newFile('cache')->at($root)->setContent('cache');
        $resourceFile = vfsStream::newFile('resource')->at($root)->setContent('resource');

        $cache = new ResourceCollectionCache($cacheFile->url(), false);
        $result = $cache->isFresh(array(new FileResource($resourceFile->url(), $resourceFile->path())));

        $this->assertTrue($result);
    }

    public function testWrite()
    {
        $root = vfsStream::setup();
        $cacheFile = vfsStream::newFile('cache')->at($root);

        $cache = new ResourceCollectionCache($cacheFile->url(), false);
        $cache->write('testContent');

        $this->assertSame('testContent', file_get_contents($cacheFile->url()));
    }
}
