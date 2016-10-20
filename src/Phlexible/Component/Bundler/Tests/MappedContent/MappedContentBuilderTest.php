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

use org\bovigo\vfs\vfsStream;
use Phlexible\Component\GuiAsset\Content\MappedContent;
use Phlexible\Component\GuiAsset\ResourceResolver\ResolvedResources;
use Phlexible\Component\GuiAsset\SourceMap\SourceMap;
use Puli\Repository\Resource\FileResource;

/**
 * @covers \Phlexible\Component\GuiAsset\MappedContent\MappedContentBuilder
 */
class MappedContentBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildCreatedMappedContent()
    {
        $root = vfsStream::setup();
        $jsFile = vfsStream::newFile('js/file.js')->at($root)->setContent('console.log(123);');

        $builder = new \Phlexible\Component\GuiAsset\ContentBuilder\MappedContentBuilder();
        $result = $builder->build(
            'test',
            new ResolvedResources(array(
                new FileResource($jsFile->url(), $jsFile->path())
            ))
        );

        $expected = new MappedContent(
            'console.log(123);' . PHP_EOL,
            (new SourceMap(
                'test', '', array($jsFile->path()), array($jsFile->getContent() . PHP_EOL), array(), 'AAAA;'
            ))->toJson()
        );

        $this->assertEquals($expected, $result);
    }

    public function testBuildCallsSanitizePathCallback()
    {
        $root = vfsStream::setup();
        $jsFile = vfsStream::newFile('js/file.js')->at($root)->setContent('console.log(123);');

        $builder = new \Phlexible\Component\GuiAsset\ContentBuilder\MappedContentBuilder();
        $result = $builder->build(
            'test',
            new ResolvedResources(array(
                new FileResource($jsFile->url(), $jsFile->path())
            )),
            function() {
                return 'sanitizedPath';
            }
        );

        $expected = new \Phlexible\Component\GuiAsset\Content\MappedContent(
            'console.log(123);' . PHP_EOL,
            (new SourceMap(
                'test', '', array('sanitizedPath'), array($jsFile->getContent() . PHP_EOL), array(), 'AAAA;'
            ))->toJson()
        );

        $this->assertEquals($expected, $result);
    }

    public function testBuildCallsPrefixContentCallback()
    {
        $root = vfsStream::setup();
        $jsFile = vfsStream::newFile('js/file.js')->at($root)->setContent('console.log(123);');

        $builder = new \Phlexible\Component\GuiAsset\ContentBuilder\MappedContentBuilder();
        $result = $builder->build(
            'test',
            new ResolvedResources(array(
                new FileResource($jsFile->url(), $jsFile->path())
            )),
            null,
            function() {
                return 'PREFIX'.PHP_EOL;
            }
        );

        $expected = new MappedContent(
            'PREFIX' . PHP_EOL . 'console.log(123);' . PHP_EOL,
            (new SourceMap(
                'test', '', array($jsFile->path()), array($jsFile->getContent() . PHP_EOL), array(), ';AAAA;'
            ))->toJson()
        );

        $this->assertEquals($expected, $result);
    }

    public function testBuildCallsFilterContentCallback()
    {
        $root = vfsStream::setup();
        $jsFile = vfsStream::newFile('js/file.js')->at($root)->setContent('console.log(123);');

        $builder = new \Phlexible\Component\GuiAsset\ContentBuilder\MappedContentBuilder();
        $result = $builder->build(
            'test',
            new ResolvedResources(array(
                new FileResource($jsFile->url(), $jsFile->path())
            )),
            null,
            null,
            function() {
                return 'FILTERED'.PHP_EOL;
            }
        );

        $expected = new MappedContent(
            'FILTERED' . PHP_EOL,
            (new SourceMap(
                'test', '', array($jsFile->path()), array($jsFile->getContent() . PHP_EOL), array(), 'AAAA;'
            ))->toJson()
        );

        $this->assertEquals($expected, $result);
    }
}
