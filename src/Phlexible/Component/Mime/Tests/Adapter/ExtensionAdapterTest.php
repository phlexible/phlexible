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

use Phlexible\Component\Mime\Adapter\ExtensionAdapter;

/**
 * Extension adapter test.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExtensionAdapterTest extends AbstractConcreteAdapterTest
{
    /**
     * @var ExtensionAdapter
     */
    protected $adapter = null;

    public function testCheckAvailability()
    {
        if (!$this->createAdapter()->isAvailable($this->getFile('test.csv'))) {
            $this->markTestSkipped('Extension adapter not available on this system');
        }
    }

    /**
     * @depends testCheckAvailability
     * @expectedException \Phlexible\Component\Mime\Exception\DetectionFailedException
     */
    public function testFileNoExtension()
    {
        $this->createAdapter()->getInternetMediaTypeStringFromFile($this->getFile('no_extension'));
    }

    /**
     * {@inheritdoc}
     */
    protected function createAdapter()
    {
        return new ExtensionAdapter();
    }
}
