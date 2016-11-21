<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Compressor;

use Phlexible\Bundle\GuiBundle\Exception\InvalidArgumentException;

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