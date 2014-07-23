<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Folder;

/**
 * Calculated size
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
