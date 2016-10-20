<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Compressor;

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
     * @param string $buffer
     *
     * @return string
     */
    public function compressString($buffer);

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
