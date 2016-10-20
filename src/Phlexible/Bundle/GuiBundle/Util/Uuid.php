<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Util;

use Rhumsaa\Uuid\Uuid as BaseUuid;

/**
 * Uuid generator wrapper
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Uuid
{
    /**
     * Generate a version 4 (random) UUID
     *
     * @return string
     */
    public static function generate()
    {
        return BaseUuid::uuid4()->toString();
    }
}


