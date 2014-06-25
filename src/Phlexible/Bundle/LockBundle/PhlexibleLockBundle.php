<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\LockBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Lock bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleLockBundle extends Bundle
{
    const RESOURCE_LOCKS = 'locks';
}
