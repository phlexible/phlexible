<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume\Folder;

/**
 * Calculated size.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CalculatedSize
{
    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $numFolders;

    /**
     * @var int
     */
    private $numFiles;

    /**
     * @param int $size
     * @param int $numFolders
     * @param int $numFiles
     */
    public function __construct($size, $numFolders, $numFiles)
    {
        $this->size = $size;
        $this->numFolders = $numFolders;
        $this->numFiles = $numFiles;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getNumFolders()
    {
        return $this->numFolders;
    }

    /**
     * @return int
     */
    public function getNumFiles()
    {
        return $this->numFiles;
    }

    /**
     * @param CalculatedSize $calculatedSize
     */
    public function merge(CalculatedSize $calculatedSize)
    {
        $this->size += $calculatedSize->getSize();
        $this->numFolders += $calculatedSize->getNumFolders();
        $this->numFiles += $calculatedSize->getNumFiles();
    }
}
