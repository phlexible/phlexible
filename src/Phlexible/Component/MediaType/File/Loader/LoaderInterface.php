<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaType\File\Loader;

use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Media type loader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderInterface
{
    /**
     * @return string
     */
    public function getExtension();

    /**
     * @param string $filename
     *
     * @return MediaType
     */
    public function load($filename);
}
