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

use Phlexible\Component\GuiAsset\Exception\InvalidArgumentException;

/**
 * Abstract string compressor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractStringCompressor implements CompressorInterface
{
    /**
     * {@inheritdoc}
     */
    public function compressFile($filename)
    {
        $string = file_get_contents($filename);
        $compressedString = $this->compressString($string);

        file_put_contents($filename, $compressedString);

        return $filename;
    }

    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function compressStream($stream)
    {
        if (!is_resource($stream)) {
            throw new InvalidArgumentException('Argument is not a resource');
        }

        rewind($stream);
        $string = stream_get_contents($stream);
        fclose($stream);
        $compressedString = $this->compressString($string);
        $stream = fopen('php://memory', 'b+');
        fwrite($stream, $compressedString);
        rewind($stream);

        return $stream;
    }
}
