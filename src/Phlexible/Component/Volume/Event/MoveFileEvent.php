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
use Phlexible\Component\Volume\Model\FolderInterface;

/**
 * Move file event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MoveFileEvent extends FileEvent
{
    /**
     * @var FolderInterface
     */
    private $targetFolder;

    /**
     * @param FileInterface   $file
     * @param FolderInterface $targetFolder
     */
    public function __construct(FileInterface $file, FolderInterface $targetFolder)
    {
        parent::__construct($file);

        $this->targetFolder = $targetFolder;
    }

    /**
     * @return FolderInterface
     */
    public function getTargetFolder()
    {
        return $this->targetFolder;
    }
}
