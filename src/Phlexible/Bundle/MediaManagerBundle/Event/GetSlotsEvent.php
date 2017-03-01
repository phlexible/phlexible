<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\Event;

use Phlexible\Component\MediaManager\Slot\Slots;
use Symfony\Component\EventDispatcher\Event;

/**
 * Get slots event.
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
