<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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


