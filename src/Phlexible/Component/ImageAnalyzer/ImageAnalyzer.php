<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
