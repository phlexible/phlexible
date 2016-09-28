<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Mime\Tests\Adapter;

use Phlexible\Component\Mime\Adapter\FileAdapter;

/**
 * File adapter test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileAdapterTest extends AbstractConcreteAdapterTest
{
    public function testCheckAvailability()
    {
        $adapter = $this->createAdapter('file');
        $this->assertTrue($adapter->isAvailable($this->getFile('file.txt')));
    }

    public function testIsNotAvailableForInvalidExecutable()
    {
        $adapter = $this->createAdapter('invalid_file_command');
        $this->assertFalse($adapter->isAvailable($this->getFile('file.txt')));
    }

    /**
     * @expectedException \Phlexible\Component\Mime\Exception\DetectionFailedException
     */
    public function testInvalidExec()
    {
        $adapter = $this->createAdapter('invalid_file_command');

        $adapter->getInternetMediaTypeStringFromFile($this->getFile('file.txt'));
    }

    /**
     * @return FileAdapter
     */
    protected function createAdapter($fileCommand = 'file')
    {
        return new FileAdapter($fileCommand);
    }
}
