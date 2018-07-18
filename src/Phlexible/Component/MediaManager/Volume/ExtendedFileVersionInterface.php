<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\Volume;

use Phlexible\Component\Volume\Model\FileVersionInterface;

/**
 * Extended file interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ExtendedFileVersionInterface extends FileVersionInterface, HasMediaTypeInterface, HasMetaSetInterface
{
}
