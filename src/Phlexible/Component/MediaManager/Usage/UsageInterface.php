<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\Usage;

/**
 * Usage interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface UsageInterface
{
    const STATUS_ONLINE = 8;
    const STATUS_LATEST = 4;
    const STATUS_OLD = 2;
    const STATUS_DEAD = 1;
}
