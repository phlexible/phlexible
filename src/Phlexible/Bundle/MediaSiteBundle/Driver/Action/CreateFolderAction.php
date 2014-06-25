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
 * Create folder action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CreateFolderAction extends FolderAction
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
