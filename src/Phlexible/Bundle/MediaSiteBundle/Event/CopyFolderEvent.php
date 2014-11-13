<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;

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
