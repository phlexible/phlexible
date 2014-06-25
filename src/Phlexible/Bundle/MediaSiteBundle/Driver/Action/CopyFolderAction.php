<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver\Action;

use Phlexible\Bundle\MediaSiteBundle\Folder\FolderInterface;

/**
 * Copy folder action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CopyFolderAction extends FolderAction
{
    /**
     * @var FolderInterface
     */
    private $targetFolder;

    /**
     * @var string
     */
    private $userId;

    /**
     * @param FolderInterface $folder
     * @param FolderInterface $targetFolder
     * @param string          $userId
     */
    public function __construct(FolderInterface $folder, FolderInterface $targetFolder, $userId)
    {
        parent::__construct($folder);

        $this->targetFolder = $targetFolder;
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
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
