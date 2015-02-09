<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Test\Compressor;

use org\bovigo\vfs\vfsStream;
use Phlexible\Bundle\GuiBundle\Compressor\SimpleJavascriptCompressor;

/**
 * Simple Javascript compressor test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class JavascriptCompressorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SimpleJavascriptCompressor
     */
    private $compressor;

    protected function setUp()
    {
        $this->compressor = new SimpleJavascriptCompressor();
    }

    private function createJs()
    {
        return <<<EOF
var x = {
    test: 1,
    bla: 2,
    blubb: 3
};
EOF;
    }

    public function testCompressString()
    {
        $js = $this->createJs();

        $compressed = $this->compressor->compressString($js);

        $this->assertEquals('var x = {test: 1,bla: 2,blubb: 3};', $compressed);
    }

    public function testCompressStream()
    {
        $js = $this->createJs();

        $stream = fopen('php://memory', 'r+');
        fputs($stream, $js);
        rewind($stream);

        $compressed = stream_get_contents($this->compressor->compressStream($stream));

        $this->assertEquals('var x = {test: 1,bla: 2,blubb: 3};', $compressed);
    }

    public function testCompressFile()
    {
        $js = $this->createJs();

        $vfs = vfsStream::setup('root', null, array('test.js' => $js));

        $compressed = file_get_contents($this->compressor->compressFile(vfsStream::url('root/test.js')));

        $this->assertEquals('var x = {test: 1,bla: 2,blubb: 3};', $compressed);
    }
}
