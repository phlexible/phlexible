<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Util\Tests;

use org\bovigo\vfs\vfsStream;
use Phlexible\Component\Util\FileLock;

/**
 * Lock file Test
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class LockFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Unique lock file name.
     *
     * @var string
     */
    private $lockFileName;

    /**
     * Set up fixture.
     */
    protected function setUp()
    {
        vfsStream::setup('test', array(), array('testfile.txt' => 'test'));

        $this->lockFileName = vfsStream::url('test/testfile.txt');
    }

    public function testConstructor()
    {
        $lock = new FileLock($this->lockFileName);

        self::assertTrue($lock instanceof FileLock);
    }

    public function testNonBlockingAcquire()
    {
        $lock = $this->createLock();

        $result = $lock->acquire();

        self::assertTrue($result);
    }

    public function testBlockingAcquire()
    {
        $lock = $this->createLock();

        $result = $lock->acquire(true);

        self::assertTrue($result);
    }

    public function testRelease()
    {
        $lock = $this->createLock();
        $lockCreationSucceed = $lock->acquire();
        self::assertTrue($lockCreationSucceed);

        $lock->release();
    }

    /**
     * @return FileLock
     */
    protected function createLock()
    {
        return new FileLock($this->lockFileName);
    }
}
