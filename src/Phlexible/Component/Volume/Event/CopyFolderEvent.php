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
 * Copy folder event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CopyFolderEvent extends FolderEvent
{
    /**
     * @var FolderInterface
     */
    private $targetFolder;

    /**
     * @param FolderInterface $folder
     * @param FolderInterface $targetFolder
     */
    public function __construct(FolderInterface $folder, FolderInterface $targetFolder)
    {
        parent::__construct($folder);

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
