<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Upload;

use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;

/**
 * Temp file
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TempFile implements PathSourceInterface
{
    /**
     * @var string
     */
    private $id;

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
     * @var string
     */
    private $fileId;

    /**
     * @var string
     */
    private $folderId;

    /**
     * @var bool
     */
    private $useWizard = false;

    /**
     * @var string
     */
    private $userId;

    /**
     * @param string $id
     * @param string $name
     * @param string $path
     * @param string $mimeType
     * @param int    $size
     * @param string $fileId
     * @param string $folderId
     * @param string $userId
     * @param bool   $useWizard
     */
    public function __construct($id, $name, $path, $mimeType, $size, $fileId, $folderId, $userId, $useWizard = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->path = $path;
        $this->mimeType = $mimeType;
        $this->size = $size;
        $this->fileId = $fileId;
        $this->folderId = $folderId;
        $this->userId = $userId;
        $this->useWizard = $useWizard;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setAlternativeName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
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

    /**
     * @return string
     */
    public function getFileId()
    {
        return $this->fileId;
    }

    /**
     * @return string
     */
    public function getFolderId()
    {
        return $this->folderId;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return boolean
     */
    public function getUseWizard()
    {
        return $this->useWizard;
    }
}
