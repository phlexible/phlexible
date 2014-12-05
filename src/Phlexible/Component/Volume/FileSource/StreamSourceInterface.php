<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Volume\FileSource;

/**
 * Stream based file source interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface StreamSourceInterface extends FileSourceInterface
{
    /**
     * @return resource
     */
    public function getStream();
}
