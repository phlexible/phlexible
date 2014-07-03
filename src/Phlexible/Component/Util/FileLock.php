<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Util;

/**
 * File system lock
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class FileLock
{
    /**
     * Name of the file for the file system lock.
     *
     * @var string
     */
    private $lockFileName;

    /**
     * The file handle for the file system lock.
     *
     * @var resource
     */
    private $lock;

    /**
     *
     */
    private $hasLock;

    /**
     * @param string $lockFileName
     * @param string $lockDir
     */
    public function __construct($lockFileName, $lockDir = null)
    {
        $this->hasLock = false;
        $this->lockFileName = $lockFileName;

        if ($lockDir) {
            $this->lockFileName = $lockDir . '/' . $this->lockFileName;
        }

        $this->openHandle();
    }

    /**
     * Close handle on destruct
     */
    public function __destruct()
    {
        $this->closeHandle();
    }

    /**
     * Acquire lock (blocking).
     * Default is a non blocking lock, provide true as first parameter for a blocking lock.
     *
     * @param bool $blocking
     *
     * @return bool
     */
    public function acquire($blocking = false)
    {
        return $this->doLock($blocking ? LOCK_EX : LOCK_EX | LOCK_NB);
    }

    /**
     * Release lock.
     */
    public function release()
    {
        $this->closeHandle();
    }

    /**
     * Do locking.
     *
     * @param integer $flags flock options
     *
     * @return bool
     */
    private function doLock($flags)
    {
        // get the lock
        $result = flock($this->lock, $flags);

        // cleanup if locking fails
        if (!$result) {
            $this->closeHandle();
        } else {
            $this->hasLock = true;
            fwrite($this->lock, getmypid());
        }

        return $result;
    }

    /**
     * Open file
     */
    private function openHandle()
    {
        if (!file_exists($this->lockFileName)) {
            if (!file_exists(dirname($this->lockFileName))) {
                if (!mkdir(dirname($this->lockFileName), 0777, true)) {
                    throw new Exception('Can\'t create lock dir');
                }
            }

            touch($this->lockFileName);
            chmod($this->lockFileName, 0777);
        }

        $this->lock = fopen($this->lockFileName, 'c');
    }

    /**
     * Close and delete lock file handle.
     */
    protected function closeHandle()
    {
        if ($this->lock) {
            if ($this->hasLock) {
                ftruncate($this->lock, 0);
            }

            fclose($this->lock);
        }

        $this->lock = null;
        $this->hasLock = false;
    }
}
