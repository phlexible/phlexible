<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaCacheBundle\Exception;

use Phlexible\Component\MediaCache\Exception\RuntimeException;

/**
 * Already running exception for media cache
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AlreadyRunningException extends RuntimeException
{

}
