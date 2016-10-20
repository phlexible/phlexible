<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume;

use Phlexible\Component\Volume\Exception\NotFoundException;

/**
 * Volume manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VolumeManager
{
    /**
     * @var Volume[]
     */
    private $volumes = [];

    /**
     * @var array
     */
    private $idToKey = [];

    /**
     * @param Volume[] $volumes
     */
    public function __construct(array $volumes)
    {
        $this->volumes = $volumes;

        foreach ($volumes as $key => $volume) {
            $this->idToKey[$volume->getId()] = $key;
        }
    }

    /**
     * @param string $key
     *
     * @return Volume
     */
    public function get($key)
    {
        return $this->volumes[$key];
    }

    /**
     * @param string $id
     *
     * @return Volume
     */
    public function getById($id)
    {
        return $this->get($this->idToKey[$id]);
    }

    /**
     * Return volume by file ID
     *
     * @param string $fileId
     *
     * @return Volume
     * @throws NotFoundException
     */
    public function getByFileId($fileId)
    {
        if ($volume = $this->findByFileId($fileId)) {
            return $volume;
        }

        throw new NotFoundException("Volume for file $fileId not found.");
    }

    /**
     * Return volume by file ID
     *
     * @param string $fileId
     *
     * @return Volume
     */
    public function findByFileId($fileId)
    {
        foreach ($this->volumes as $volume) {
            try {
                $file = $volume->findFile($fileId);
                if ($file) {
                    return $volume;
                }
            } catch (\Exception $e) {
            }
        }

        return null;
    }

    /**
     * Return volume by folder ID
     *
     * @param string $folderId
     *
     * @return Volume
     * @throws NotFoundException
     */
    public function getByFolderId($folderId)
    {
        if ($volume = $this->findByFolderId($folderId)) {
            return $volume;
        }

        throw new NotFoundException("Volume for folder $folderId not found.");
    }

    /**
     * Return volume by folder ID
     *
     * @param string $folderId
     *
     * @return Volume
     */
    public function findByFolderId($folderId)
    {
        foreach ($this->volumes as $volume) {
            if ($volume->findFolder($folderId)) {
                return $volume;
            }
        }

        return null;
    }

    /**
     * Return all volumes
     *
     * @return Volume[]
     */
    public function all()
    {
        return $this->volumes;
    }
}
