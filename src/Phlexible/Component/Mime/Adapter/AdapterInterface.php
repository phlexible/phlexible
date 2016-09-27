<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Mime\Adapter;

/**
 * Internet media type detector adapter interface
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
interface AdapterInterface
{
    /**
     * Check if this adapter is available
     *
     * @param string $filename
     *
     * @return boolean
     */
    public function isAvailable($filename);

    /**
     * Return internet media type string from file
     *
     * @param string $filename
     *
     * @return string
     */
    public function getInternetMediaTypeStringFromFile($filename);
}
