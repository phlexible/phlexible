<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume\FileSource;

use Phlexible\Component\Volume\Exception\InvalidArgumentException;

/**
 * Stream file source
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class StreamFileSource implements StreamSourceInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var resource
     */
    private $stream;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var int
     */
    private $size;

    /**
     * @param string   $name
     * @param resource $stream
     * @param string   $mimeType
     * @param int      $size
     *
     * @throws InvalidArgumentException
     */
    public function __construct($name, $stream, $mimeType, $size)
    {
        if (!is_resource($stream)) {
            throw new InvalidArgumentException('Not a stream.');
        }

        $this->name = $name;
        $this->stream = $stream;
        $this->mimeType = $mimeType;
        $this->size = $size;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }
}
