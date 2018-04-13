<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Worker;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaManager\Volume\ExtendedFileVersionInterface;

/**
 * Input descriptor.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class InputDescriptor
{
    /**
     * @var string
     */
    private $volumeId;

    /**
     * @var string
     */
    private $fileId;

    /**
     * @var int
     */
    private $fileVersion;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $fileHash;

    /**
     * @var array
     */
    private $fileAttributes;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var string
     */
    private $mediaType;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @param string $volumeId
     * @param string $fileId
     * @param int    $fileVersion
     * @param string $fileName
     * @param string $fileHash
     * @param array  $fileAttributes
     * @param string $mimeType
     * @param string $mediaType
     * @param string $filePath
     */
    public function __construct($volumeId, $fileId, $fileVersion, $fileName, $fileHash, array $fileAttributes, $mimeType, $mediaType, $filePath)
    {
        $this->volumeId = $volumeId;
        $this->fileId = $fileId;
        $this->fileVersion = $fileVersion;
        $this->fileName = $fileName;
        $this->fileHash = $fileHash;
        $this->fileAttributes = $fileAttributes;
        $this->mimeType = $mimeType;
        $this->mediaType = $mediaType;
        $this->filePath = $filePath;
    }

    public static function fromFile(ExtendedFileInterface $file)
    {
        return new self(
            $file->getVolume()->getId(),
            $file->getId(),
            $file->getVersion(),
            $file->getName(),
            $file->getHash(),
            $file->getAttributes(),
            $file->getMimeType(),
            $file->getMediaType(),
            $file->getPhysicalPath()
        );
    }

    public static function fromFileVersion(ExtendedFileVersionInterface $fileVersion)
    {
        return new self(
            $fileVersion->getFile()->getVolume()->getId(),
            $fileVersion->getFileId(),
            $fileVersion->getVersion(),
            $fileVersion->getName(),
            $fileVersion->getHash(),
            $fileVersion->getAttributes(),
            $fileVersion->getMimeType(),
            $fileVersion->getMediaType(),
            $fileVersion->getPhysicalPath()
        );
    }

    /**
     * @return string
     */
    public function getVolumeId()
    {
        return $this->volumeId;
    }

    /**
     * @return string
     */
    public function getFileId()
    {
        return $this->fileId;
    }

    /**
     * @return int
     */
    public function getFileVersion()
    {
        return $this->fileVersion;
    }

    /**
     * @return string
     */
    public function getFileHash()
    {
        return $this->fileHash;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return array
     */
    public function getFileAttributes()
    {
        return $this->fileAttributes;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getMediaType()
    {
        return $this->mediaType;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }
}
