<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Mime\Tests\Adapter;

/**
 * Abstract concrete adapter test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractConcreteAdapterTest extends AbstractAdapterTest
{
    /**
     * @expectedException \Phlexible\Component\Mime\Exception\FileNotFoundException
     */
    public function testFileNotFound()
    {
        $this->createAdapter()->getInternetMediaTypeStringFromFile($this->getFile('absent_file.txt'));
    }

    /**
     * @depends testCheckAvailability
     * @expectedException \Phlexible\Component\Mime\Exception\NotAFileException
     */
    public function testNotAFile()
    {
        $this->createAdapter()->getInternetMediaTypeStringFromFile($this->getFile());
    }

    /**
     * @depends testCheckAvailability
     */
    public function testUnknownFile()
    {
        $mimeType = $this->createAdapter()->getInternetMediaTypeStringFromFile($this->getFile('file.unknown'));

        $this->assertContains($mimeType, ['application/octet-stream', 'application/octet-stream; charset=binary']);
    }

    /**
     * @depends testCheckAvailability
     */
    public function testFileNoExtension()
    {
        $mimeType = $this->createAdapter()->getInternetMediaTypeStringFromFile($this->getFile('no_extension'));

        $this->assertContains($mimeType, ['text/plain', 'text/plain; charset=us-ascii']);
    }

    /**
     * @depends testCheckAvailability
     */
    public function testFiles()
    {
        $adapter = $this->createAdapter();
        $fails = [];

        foreach ($this->fileMap as $file => $expectedMimeTypes) {
            try {
                $mimeType = $adapter->getInternetMediaTypeStringFromFile($this->getFile($file));
            } catch (\Exception $e) {
                $this->fail('Unexpected exception: '.get_class($e).' ('.$e->getMessage().')');
            }

            if (!in_array($mimeType, $expectedMimeTypes)) {
                $fails[] = $file.': got '.$mimeType.', expected '.implode(' | ', $expectedMimeTypes);
            }
        }

        if (count($fails)) {
            $this->markTestIncomplete(implode(PHP_EOL, $fails));
        }
    }
}
