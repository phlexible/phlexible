<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\ImageAnalyzer\Driver;

use Phlexible\Component\ImageAnalyzer\ImageInfo;

/**
 * Image analyzer driver interface.
 *
 * @author Stephan Wentz <stephan@wentz.it>
 */
interface DriverInterface
{
    /**
     * @param string $filename
     *
     * @return bool
     */
    public function isAvailable($filename = null);

    /**
     * @param string $filename
     *
     * @return ImageInfo
     */
    public function analyze($filename);
}
