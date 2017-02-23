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

/**
 * Rename file event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RenameFileEvent extends FileEvent
{
    /**
     * @var string
     */
    private $oldName;

    /**
     * @param FileInterface $file
     * @param string        $oldName
     */
    public function __construct(FileInterface $file, $oldName)
    {
        parent::__construct($file);

        $this->oldName = $oldName;
    }

    /**
     * @return string
     */
    public function getOldName()
    {
        return $this->oldName;
    }
}
