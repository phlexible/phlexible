<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Before save meta event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeSaveMetaEvent extends Event
{
    //private $eventName = Events::BEFORE_SAVE_META;

    /**
     * @var FileInterface
     */
    private $file;

    /**
     * @param FileInterface $file
     */
    public function __construct(FileInterface $file)
    {
        $this->file = $file;
    }

    /**
     * @return FileInterface
     */
    public function getFile()
    {
        return $this->file;
    }
}