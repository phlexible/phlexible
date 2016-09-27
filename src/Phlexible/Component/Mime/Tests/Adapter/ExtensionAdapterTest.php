<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Mime\Tests\Adapter;

use Phlexible\Component\Mime\Adapter\ExtensionAdapter;

/**
 * Extension adapter test
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
        if (!$this->createAdapter()->isAvailable('testFile')) {
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
