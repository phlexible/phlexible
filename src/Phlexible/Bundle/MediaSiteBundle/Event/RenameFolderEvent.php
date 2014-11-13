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
 * Rename folder event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RenameFolderEvent extends FolderEvent
{
    /**
     * @var string
     */
    private $oldPath;

    /**
     * @param FolderInterface $folder
     * @param string          $oldPath
     */
    public function __construct(FolderInterface $folder, $oldPath)
    {
        parent::__construct($folder);

        $this->oldPath = $oldPath;
    }

    /**
     * @return string
     */
    public function getOldName()
    {
        return $this->oldPath;
    }
}
