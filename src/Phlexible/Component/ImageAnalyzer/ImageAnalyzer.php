<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\ImageAnalyzer;

use Phlexible\Component\ImageAnalyzer\Driver\DriverInterface;

/**
 * Image analyzer
 *
 * @author Stephan Wentz <stephan@wentz.it>
 */
class ImageAnalyzer
{
    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @param DriverInterface $driver
     */
    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @parm string $filename
     *
     * @return ImageInfo
     */
    public function analyze($filename)
    {
        return $this->driver->analyze($filename);
    }
}
