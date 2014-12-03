<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Event;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
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
     * @var ExtendedFileInterface
     */
    private $file;

    /**
     * @param ExtendedFileInterface $file
     */
    public function __construct(ExtendedFileInterface $file)
    {
        $this->file = $file;
    }

    /**
     * @return ExtendedFileInterface
     */
    public function getFile()
    {
        return $this->file;
    }
}
