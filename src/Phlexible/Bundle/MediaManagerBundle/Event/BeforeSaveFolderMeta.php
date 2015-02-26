<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Event;

use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Before save folder meta event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeSaveFolderMetaEvent extends Event
{
    /**
     * @var ExtendedFolderInterface
     */
    private $folder;

    /**
     * @param ExtendedFolderInterface $folder
     */
    public function __construct(ExtendedFolderInterface $folder)
    {
        $this->folder = $folder;
    }

    /**
     * @return ExtendedFolderInterface
     */
    public function getFolder()
    {
        return $this->folder;
    }
}
