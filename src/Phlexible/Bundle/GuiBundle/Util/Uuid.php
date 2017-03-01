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

use Phlexible\Component\Util\UuidUtil;

/**
 * Uuid generator wrapper.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @deprecated
 */
class Uuid extends UuidUtil
{
    public static function generate()
    {
        trigger_error(__METHOD__.' is deprecated. Use '.UuidUtil::class.'::generate() instead.', E_USER_DEPRECATED);

        return parent::generate();
    }
}
