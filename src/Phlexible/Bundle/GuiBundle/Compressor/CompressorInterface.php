<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Compressor;

/**
 * Compressor interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface CompressorInterface
{
    /**
     * Compress given string
     *
     * @param string $string
     *
     * @return string
     */
    public function compressString($string);

    /**
     * Compress given file
     * Replaces the pointed out file with the compressed version
     *
     * @param string $filename
     *
     * @return string
     */
    public function compressFile($filename);

    /**
     * Compress given stream
     *
     * @param resource $stream
     *
     * @return string
     */
    public function compressStream($stream);
}
