<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Abstract folder event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AbstractFolderEvent extends Event
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