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
    private $oldName;

    /**
     * @param FolderInterface $folder
     * @param string          $oldName
     */
    public function __construct(FolderInterface $folder, $oldName)
    {
        parent::__construct($folder);

        $this->oldName = $oldName;
    }

    /**
     * @return string
     */
    public function getOldName()
    {
        return $this->oldName;
    }
}