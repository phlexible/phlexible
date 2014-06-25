<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Event;

use Phlexible\Bundle\MediaManagerBundle\Slot\Slots;
use Symfony\Component\EventDispatcher\Event;

/**
 * Get slots event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetSlotsEvent extends Event
{
    /**
     * @var Slots
     */
    private $slots;

    /**
     * @param Slots $slots
     */
    public function __construct(Slots $slots)
    {
        $this->slots = $slots;
    }

    /**
     * @return Slots
     */
    public function getSlots()
    {
        return $this->slots;
    }
}