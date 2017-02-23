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

/**
 * Filesystem file source.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FilesystemFileSource implements PathSourceInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var int
     */
    private $size;

    /**
     * @param string $path
     * @param string $mimeType
     * @param int    $size
     */
    public function __construct($path, $mimeType, $size)
    {
        $this->name = basename($path);
        $this->path = $path;
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
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->size;
    }
}
