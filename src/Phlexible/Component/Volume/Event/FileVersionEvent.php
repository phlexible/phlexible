<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume\Event;

use Phlexible\Component\Volume\Model\FileVersionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * File version event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileVersionEvent extends Event
{
    /**
     * @var FileVersionInterface
     */
    private $fileVersion;

    /**
     * @param FileVersionInterface $fileVersion
     */
    public function __construct(FileVersionInterface $fileVersion)
    {
        $this->fileVersion = $fileVersion;
    }

    /**
     * @return FileVersionInterface
     */
    public function getFileVersion()
    {
        return $this->fileVersion;
    }
}
