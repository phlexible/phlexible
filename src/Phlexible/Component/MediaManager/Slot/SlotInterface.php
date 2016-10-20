<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\Slot;

/**
 * Slots interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SlotInterface
{
    /**
     * @return string
     */
    public function getKey();

    /**
     * @return array
     */
    public function getData();
}
