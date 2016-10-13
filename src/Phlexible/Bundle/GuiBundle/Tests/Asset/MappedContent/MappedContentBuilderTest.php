<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Tests\Asset\MappedContent;

use org\bovigo\vfs\vfsStream;
use Phlexible\Bundle\GuiBundle\Asset\MappedContent\MappedContent;
use Phlexible\Bundle\GuiBundle\Asset\MappedContent\MappedContentBuilder;
use Phlexible\Bundle\GuiBundle\Asset\SourceMap\SourceMap;
use Puli\Repository\Resource\FileResource;

/**
 * @covers \Phlexible\Bundle\GuiBundle\Asset\MappedContent\MappedContentBuilder
 */
class MappedContentBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildCreatedMappedContent()
    {
        $root = vfsStream::setup();
        $jsFile = vfsStream::newFile('js/file.js')->at($root)->setContent('console.log(123);');

        $builder = new MappedContentBuilder();
        $result = $builder->build(
            'test',
            array(
                new FileResource($jsFile->url(), $jsFile->path())
            )
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

        $builder = new MappedContentBuilder();
        $result = $builder->build(
            'test',
            array(
                new FileResource($jsFile->url(), $jsFile->path())
            ),
            function() {
                return 'sanitizedPath';
            }
        );

        $expected = new MappedContent(
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

        $builder = new MappedContentBuilder();
        $result = $builder->build(
            'test',
            array(
                new FileResource($jsFile->url(), $jsFile->path())
            ),
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

        $builder = new MappedContentBuilder();
        $result = $builder->build(
            'test',
            array(
                new FileResource($jsFile->url(), $jsFile->path())
            ),
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
