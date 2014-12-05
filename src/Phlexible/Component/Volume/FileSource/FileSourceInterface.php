<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Volume\FileSource;

/**
 * File source interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface FileSourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getMimeType();

    /**
     * @return int
     */
    public function getSize();
}
