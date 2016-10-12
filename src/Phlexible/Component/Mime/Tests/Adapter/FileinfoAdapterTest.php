<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Mime\Tests\Adapter;

use Phlexible\Component\Mime\Adapter\FileinfoAdapter;

/**
 * Fileinfo adapter test
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileinfoAdapterTest extends AbstractConcreteAdapterTest
{
    public function testCheckAvailability()
    {
        if (!$this->createAdapter()->isAvailable('testFile')) {
            $this->markTestSkipped('Fileinfo adapter not available on this system');
        }
    }

    /**
     * @depends testCheckAvailability
     * @expectedException \Phlexible\Component\Mime\Exception\FileNotFoundException
     */
    public function testInstanciationWithInvalidMagicFile()
    {
        $adapter = $this->createAdapter('invalidfile');

        $this->assertFalse($adapter->isAvailable('testFile'));
    }

    /**
     * {@inheritdoc}
     */
    protected function createAdapter($magicFile = null)
    {
        return new FileinfoAdapter($magicFile);
    }
}
