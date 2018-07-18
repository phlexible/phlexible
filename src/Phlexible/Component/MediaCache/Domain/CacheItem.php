<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * Media cache item.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity(repositoryClass = "Phlexible\Bundle\MediaCacheBundle\Entity\Repository\CacheItemRepository")
 * @ORM\Table(name="media_cache", uniqueConstraints={@ORM\UniqueConstraint(columns={"template_key", "file_id", "file_version"})})
 */
class CacheItem
{
    const STATUS_WAITING = 'waiting';
    const STATUS_OK = 'ok';
    const STATUS_DELEGATE = 'delegate';
    const STATUS_ERROR = 'error';
    const STATUS_MISSING = 'missing';
    const STATUS_DELETED = 'deleted';

    const QUEUE_WAITING = 'waiting';
    const QUEUE_ERROR = 'error';
    const QUEUE_DONE = 'done';
    const QUEUE_NOT_APPLICABLE = 'not_applicable';

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=32, options={"fixed"=true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="volume_id", type="string", length=36, options={"fixed"=true})
     */
    private $volumeId;

    /**
     * @var string
     * @ORM\Column(name="file_id", type="string", length=36, options={"fixed"=true})
     */
    private $fileId;

    /**
     * @var int
     * @ORM\Column(name="file_version", type="integer")
     */
    private $fileVersion = 1;

    /**
     * @var string
     * @ORM\Column(name="template_key", type="string", length=100)
     */
    private $templateKey;

    /**
     * @var int
     * @ORM\Column(name="template_revision", type="integer")
     */
    private $templateRevision;

    /**
     * @var string
     * @ORM\Column(name="cache_status", type="string", length=20)
     */
    private $cacheStatus;

    /**
     * @var string
     * @ORM\Column(name="queue_status", type="string", length=20)
     */
    private $queueStatus;

    /**
     * @var string
     * @ORM\Column(name="mime_type", type="string", length=100, nullable=true)
     */
    private $mimeType;

    /**
     * @var string
     * @ORM\Column(name="media_type", type="string", length=100, nullable=true)
     */
    private $mediaType;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $extension;

    /**
     * @var int
     * @ORM\Column(name="file_size", type="integer", nullable=true)
     */
    private $fileSize;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $width;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $height;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $error;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="queued_at", type="datetime", nullable=true)
     */
    private $queuedAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="finished_at", type="datetime", nullable=true)
     */
    private $finishedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getVolumeId()
    {
        return $this->volumeId;
    }

    /**
     * @param string $volumeId
     *
     * @return $this
     */
    public function setVolumeId($volumeId)
    {
        $this->volumeId = $volumeId;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileId()
    {
        return $this->fileId;
    }

    /**
     * @param string $fileId
     *
     * @return $this
     */
    public function setFileId($fileId)
    {
        $this->fileId = $fileId;

        return $this;
    }

    /**
     * @return int
     */
    public function getFileVersion()
    {
        return $this->fileVersion;
    }

    /**
     * @param int $fileVersion
     *
     * @return $this
     */
    public function setFileVersion($fileVersion)
    {
        $this->fileVersion = $fileVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateKey()
    {
        return $this->templateKey;
    }

    /**
     * @param string $templateKey
     *
     * @return $this
     */
    public function setTemplateKey($templateKey)
    {
        $this->templateKey = $templateKey;

        return $this;
    }

    /**
     * @return int
     */
    public function getTemplateRevision()
    {
        return $this->templateRevision;
    }

    /**
     * @param int $templateRevision
     *
     * @return $this
     */
    public function setTemplateRevision($templateRevision)
    {
        $this->templateRevision = $templateRevision;

        return $this;
    }

    /**
     * @return string
     */
    public function getCacheStatus()
    {
        return $this->cacheStatus;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setCacheStatus($status)
    {
        $this->cacheStatus = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     *
     * @return $this
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * @return string
     */
    public function getMediaType()
    {
        return $this->mediaType;
    }

    /**
     * @param string $mediaType
     *
     * @return $this
     */
    public function setMediaType($mediaType)
    {
        $this->mediaType = $mediaType;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     *
     * @return $this
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @param int $fileSize
     *
     * @return $this
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     *
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     *
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     *
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getQueueStatus()
    {
        return $this->queueStatus;
    }

    /**
     * @param string $queueStatus
     *
     * @return $this
     */
    public function setQueueStatus($queueStatus)
    {
        $this->queueStatus = $queueStatus;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getQueuedAt()
    {
        return $this->queuedAt;
    }

    /**
     * @param \DateTime $queuedAt
     *
     * @return $this
     */
    public function setQueuedAt(\DateTime $queuedAt = null)
    {
        $this->queuedAt = $queuedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * @param \DateTime $finishedAt
     *
     * @return $this
     */
    public function setFinishedAt(\DateTime $finishedAt = null)
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }
}
