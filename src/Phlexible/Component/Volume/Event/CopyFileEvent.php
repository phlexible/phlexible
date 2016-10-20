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
 * Copy file event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CopyFileEvent extends FileEvent
{
    /**
     * @var FileInterface
     */
    private $originalFile;

    /**
     * @var FolderInterface
     */
    private $targetFolder;

    /**
     * @param FileInterface   $file
     * @param FileInterface   $originalFile
     * @param FolderInterface $targetFolder
     */
    public function __construct(FileInterface $file, FileInterface $originalFile, FolderInterface $targetFolder)
    {
        parent::__construct($file);

        $this->targetFolder = $targetFolder;
    }

    /**
     * @return FileInterface
     */
    public function getOriginalFile()
    {
        return $this->originalFile;
    }

    /**
     * @return FolderInterface
     */
    public function getTargetFolder()
    {
        return $this->targetFolder;
    }
}
