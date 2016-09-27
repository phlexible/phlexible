<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\ImageAnalyzer\Driver;

use Phlexible\Component\ImageAnalyzer\ImageInfo;

/**
 * Image analyzer driver interface
 *
 * @author Stephan Wentz <stephan@wentz.it>
 */
interface DriverInterface
{
    /**
     * @param string $filename
     * @return boolean
     */
    public function isAvailable($filename = null);

    /**
     * @param string $filename
     * @return ImageInfo
     */
    public function analyze($filename);
}
