<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Upload;

/**
 * Temp handler
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TempFile extends UploadFile
{
    /**
     * @param string $tempName
     * @param string $name
     * @param string $type
     * @param int    $size
     * @param int    $error
     */
    public function __construct($tempName, $name, $type, $size, $error)
    {
        parent::__construct($tempName, $name, $type, $size, $error);
    }
}
