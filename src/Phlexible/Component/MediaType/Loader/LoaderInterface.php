<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaType\Loader;

use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Media type loader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderInterface
{
    /**
     * Returns whether this class supports the given resource.
     *
     * @param string $file
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($file);

    /**
     * @param string $filename
     *
     * @return MediaType
     */
    public function load($filename);
}
