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

use Phlexible\Component\Volume\Model\FileInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * File event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileEvent extends Event
{
    /**
     * @var FileInterface
     */
    private $file;

    /**
     * @param FileInterface $file
     */
    public function __construct(FileInterface $file)
    {
        $this->file = $file;
    }

    /**
     * @return FileInterface
     */
    public function getFile()
    {
        return $this->file;
    }
}
