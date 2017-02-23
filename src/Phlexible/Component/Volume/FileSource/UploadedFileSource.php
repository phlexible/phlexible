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
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Upload file source.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UploadedFileSource implements PathSourceInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
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
     * @param UploadedFile $file
     * @param string       $mimeType
     *
     * @throws InvalidArgumentException
     */
    public function __construct(UploadedFile $file, $mimeType = null)
    {
        if ($file->getError()) {
            throw new InvalidArgumentException('Error in upload: '.$file->getError());
        }
        if (!$file->getClientOriginalName()) {
            throw new InvalidArgumentException('Missing name.');
        }
        if (!$file->getType()) {
            throw new InvalidArgumentException('Missing type.');
        }
        if (!is_uploaded_file($file->getPathname())) {
            throw new InvalidArgumentException('Not an uploaded file: '.$file->getPathname());
        }

        $this->name = $file->getClientOriginalName();
        $this->path = $file->getPathname();
        $this->size = $file->getSize();

        if ($mimeType) {
            $this->mimeType = $mimeType;
        } else {
            $this->mimeType = $file->getMimeType();
        }
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
