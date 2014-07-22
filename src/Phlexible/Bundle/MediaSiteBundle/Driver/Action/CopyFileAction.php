<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver\Action;

use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;

/**
 * Copy file action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CopyFileAction extends FileAction
{
    /**
     * @var FolderInterface
     */
    private $targetFolder;

    /**
     * @param FileInterface   $file
     * @param FolderInterface $targetFolder
     * @param \DateTime       $date
     * @param string          $userId
     */
    public function __construct(
        FileInterface $file,
        FolderInterface $targetFolder,
        \DateTime $date,
        $userId)
    {
        parent::__construct($file, $date, $userId);

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
