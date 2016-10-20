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
