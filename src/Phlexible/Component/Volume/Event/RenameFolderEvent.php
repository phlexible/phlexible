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

use Phlexible\Component\Volume\Model\FolderInterface;

/**
 * Rename folder event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RenameFolderEvent extends FolderEvent
{
    /**
     * @var string
     */
    private $oldPath;

    /**
     * @param FolderInterface $folder
     * @param string          $oldPath
     */
    public function __construct(FolderInterface $folder, $oldPath)
    {
        parent::__construct($folder);

        $this->oldPath = $oldPath;
    }

    /**
     * @return string
     */
    public function getOldName()
    {
        return $this->oldPath;
    }
}
