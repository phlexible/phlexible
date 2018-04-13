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

use Phlexible\Component\Volume\FileSource\FileSourceInterface;
use Phlexible\Component\Volume\Model\FileVersionInterface;

/**
 * Replace file version event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReplaceFileVersionEvent extends FileVersionEvent
{
    /**
     * @var FileSourceInterface
     */
    private $fileSource;

    /**
     * @param FileVersionInterface $fileVersion
     * @param FileSourceInterface  $fileSource
     */
    public function __construct(FileVersionInterface $fileVersion, FileSourceInterface $fileSource)
    {
        parent::__construct($fileVersion);

        $this->fileSource = $fileSource;
    }

    /**
     * @return FileSourceInterface
     */
    public function getFileSource()
    {
        return $this->fileSource;
    }
}
