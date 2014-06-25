<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver\Action;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderInterface;

/**
 * Move file action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MoveFileAction extends FileAction
{
    /**
     * @var FolderInterface
     */
    private $targetFolder;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $userId;

    /**
     * @param FileInterface   $file
     * @param FolderInterface $targetFolder
     * @param \DateTime       $date
     * @param string          $userId
     */
    public function __construct(FileInterface $file, FolderInterface $targetFolder, \DateTime $date, $userId)
    {
        parent::__construct($file);

        $this->targetFolder = $targetFolder;
        $this->date = $date;
        $this->userId = $userId;
    }

    /**
     * @return FolderInterface
     */
    public function getTargetFolder()
    {
        return $this->targetFolder;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    public function getUserId()
    {
        return $this->userId;
    }
}
