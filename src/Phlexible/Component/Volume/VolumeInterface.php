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

use Phlexible\Component\Volume\Driver\DriverInterface;
use Phlexible\Component\Volume\FileSource\FileSourceInterface;
use Phlexible\Component\Volume\Model\FileInterface;
use Phlexible\Component\Volume\Model\FileVersionInterface;
use Phlexible\Component\Volume\Model\FolderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Volume interface
 * Represents a complete set of classes used to get a virtual set of folders and files.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface VolumeInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getRootDir();

    /**
     * @return int
     */
    public function getQuota();

    /**
     * @return DriverInterface
     */
    public function getDriver();

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher();

    /**
     * @return FolderInterface
     */
    public function findRootFolder();

    /**
     * @param string $id
     *
     * @return FolderInterface
     */
    public function findFolder($id);

    /**
     * @param int $fileId
     *
     * @return FolderInterface
     */
    public function findFolderByFileId($fileId);

    /**
     * @param string $path
     *
     * @return FolderInterface
     */
    public function findFolderByPath($path);

    /**
     * @param FolderInterface $parentFolder
     *
     * @return FolderInterface[]
     */
    public function findFoldersByParentFolder(FolderInterface $parentFolder);

    /**
     * @param FolderInterface $parentFolder
     *
     * @return int
     */
    public function countFoldersByParentFolder(FolderInterface $parentFolder);

    /**
     * @param int $id
     *
     * @return FileInterface
     */
    public function findFile($id);

    /**
     * @param array      $criteria
     * @param array|null $order
     * @param int|null   $limit
     * @param int|null   $start
     *
     * @return FileInterface[]
     */
    public function findFiles(array $criteria, $order = null, $limit = null, $start = null);

    /**
     * @param array $criteria
     *
     * @return int
     */
    public function countFiles(array $criteria);

    /**
     * @param string $path
     *
     * @return FileInterface
     */
    public function findFileByPath($path);

    /**
     * @param string $id
     * @param array  $orderBy
     * @param int    $limit
     * @param int    $start
     *
     * @return FileVersionInterface[]
     */
    public function findFileVersions($id, array $orderBy = null, $limit = null, $start = null);

    /**
     * @param string $id
     * @param array  $orderBy
     *
     * @return FileVersionInterface
     */
    public function findOneFileVersion($id, array $orderBy = null);

    /**
     * @param string $id
     * @param int    $version
     *
     * @return FileVersionInterface
     */
    public function findFileVersion($id, $version);

    /**
     * @param FolderInterface $folder
     * @param string          $order
     * @param int             $limit
     * @param int             $start
     * @param bool            $includeHidden
     *
     * @return FileInterface[]
     */
    public function findFilesByFolder(
        FolderInterface $folder,
        $order = null,
        $limit = null,
        $start = null,
        $includeHidden = false);

    /**
     * @param FolderInterface $folder
     *
     * @return int
     */
    public function countFilesByFolder(FolderInterface $folder);

    /**
     * @param int $limit
     *
     * @return FileInterface[]
     */
    public function findLatestFiles($limit = 20);

    /**
     * @param string $query
     *
     * @return FileInterface[]
     */
    public function search($query);

    /**
     * @param FolderInterface $targetFolder
     * @param string          $name
     * @param array           $attributes
     * @param string          $userId
     *
     * @return FolderInterface
     */
    public function createFolder(FolderInterface $targetFolder, $name, array $attributes, $userId);

    /**
     * @param FolderInterface $folder
     * @param string          $name
     * @param string          $userId
     *
     * @return FolderInterface
     */
    public function renameFolder(FolderInterface $folder, $name, $userId);

    /**
     * @param FolderInterface $folder
     * @param FolderInterface $targetFolder
     * @param string          $userId
     *
     * @return FolderInterface
     */
    public function moveFolder(FolderInterface $folder, FolderInterface $targetFolder, $userId);

    /**
     * @param FolderInterface $folder
     * @param FolderInterface $targetFolder
     * @param string          $userId
     *
     * @return FolderInterface
     */
    public function copyFolder(FolderInterface $folder, FolderInterface $targetFolder, $userId);

    /**
     * @param FolderInterface $folder
     */
    public function checkDeleteFolder(FolderInterface $folder);

    /**
     * @param FolderInterface $folder
     * @param string          $userId
     *
     * @return FolderInterface
     */
    public function deleteFolder(FolderInterface $folder, $userId);

    /**
     * @param FolderInterface $folder
     * @param array           $attributes
     * @param string          $userId
     *
     * @return FolderInterface
     */
    public function setFolderAttributes(FolderInterface $folder, array $attributes, $userId);

    /**
     * @param FolderInterface     $targetFolder
     * @param FileSourceInterface $fileSource
     * @param array               $attributes
     * @param string              $userId
     *
     * @return FileInterface
     */
    public function createFile(
        FolderInterface $targetFolder,
        FileSourceInterface $fileSource,
        array $attributes,
        $userId
    );

    /**
     * @param FileInterface       $targetFile
     * @param FileSourceInterface $fileSource
     * @param array               $attributes
     * @param string              $userId
     *
     * @return FileVersionInterface
     */
    public function createFileVersion(
        FileInterface $targetFile,
        FileSourceInterface $fileSource,
        array $attributes,
        $userId
    );

    /**
     * @param FileInterface        $file
     * @param FileVersionInterface $fileVersion
     * @param string               $userId
     */
    public function activateFileVersion(FileInterface $file, FileVersionInterface $fileVersion, $userId);

    /**
     * @param FileInterface       $file
     * @param FileSourceInterface $fileSource
     * @param array               $attributes
     * @param string              $userId
     *
     * @return FileInterface
     */
    public function replaceFile(
        FileInterface $file,
        FileSourceInterface $fileSource,
        array $attributes,
        $userId
    );

    /**
     * @param FileInterface $file
     * @param string        $name
     * @param string        $userId
     *
     * @return FileInterface
     */
    public function renameFile(FileInterface $file, $name, $userId);

    /**
     * @param FileInterface   $file
     * @param FolderInterface $targetFolder
     * @param string          $userId
     *
     * @return FileInterface
     */
    public function moveFile(FileInterface $file, FolderInterface $targetFolder, $userId);

    /**
     * @param FileInterface   $file
     * @param FolderInterface $targetFolder
     * @param string          $userId
     *
     * @return FileInterface
     */
    public function copyFile(FileInterface $file, FolderInterface $targetFolder, $userId);

    /**
     * @param FileInterface $file
     */
    public function checkDeleteFile(FileInterface $file);

    /**
     * @param FileInterface $file
     * @param string        $userId
     *
     * @return FileInterface
     */
    public function deleteFile(FileInterface $file, $userId);

    /**
     * @param FileInterface $file
     * @param string        $userId
     *
     * @return FileInterface
     */
    public function hideFile(FileInterface $file, $userId);

    /**
     * @param FileInterface $file
     * @param string        $userId
     *
     * @return FileInterface
     */
    public function showFile(FileInterface $file, $userId);

    /**
     * @param FileInterface $file
     * @param array         $attributes
     * @param string        $userId
     *
     * @return FileInterface
     */
    public function setFileAttributes(FileInterface $file, array $attributes, $userId);

    /**
     * @param FileVersionInterface $fileVersion
     * @param array                $attributes
     * @param string               $userId
     *
     * @return FileVersionInterface
     */
    public function setFileVersionAttributes(FileVersionInterface $fileVersion, array $attributes, $userId);

    /**
     * @param FileInterface $file
     * @param string        $mimeType
     * @param string        $userId
     *
     * @return FileInterface
     */
    public function setFileMimeType(FileInterface $file, $mimeType, $userId);

    /**
     * @param FileVersionInterface $fileVersion
     * @param string               $mimeType
     * @param string               $userId
     *
     * @return FileVersionInterface
     */
    public function setFileVersionMimeType(FileVersionInterface $fileVersion, $mimeType, $userId);
}
