<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Folder\FolderInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Before save folder meta event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeSaveFolderMetaEvent extends Event
{
    /**
     * @var FolderInterface
     */
    private $folder;

    /**
     * @param FolderInterface $folder
     */
    public function __construct(FolderInterface $folder)
    {
        $this->folder = $folder;
    }

    /**
     * @return FolderInterface
     */
    public function getFolder()
    {
        return $this->folder;
    }
}
