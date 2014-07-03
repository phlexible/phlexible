<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Usage;

/**
 * Usage interface
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
