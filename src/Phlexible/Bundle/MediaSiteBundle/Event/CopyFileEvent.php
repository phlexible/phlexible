<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;

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