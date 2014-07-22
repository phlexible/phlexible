<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver\Action;

use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;

/**
 * Move folder action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MoveFolderAction extends FolderAction
{
    /**
     * @var FolderInterface
     */
    private $targetFolder;

    /**
     * @param FolderInterface $folder
     * @param FolderInterface $targetFolder
     * @param \DateTime       $date
     * @param string          $userId
     */
    public function __construct(FolderInterface $folder, FolderInterface $targetFolder, \DateTime $date, $userId)
    {
        parent::__construct($folder, $date, $userId);

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
