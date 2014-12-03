<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Volume\Event;

use Phlexible\Component\Volume\Model\FolderInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Abstract folder event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderEvent extends Event
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
