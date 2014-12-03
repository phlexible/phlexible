<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Volume\FileSource;

/**
 * Path based file source interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface PathSourceInterface extends FileSourceInterface
{
    /**
     * @return string
     */
    public function getPath();
}
