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

use Phlexible\Component\Util\UuidUtil;
use Phlexible\Component\Volume\Driver\DriverInterface;
use Phlexible\Component\Volume\Event\CopyFileEvent;
use Phlexible\Component\Volume\Event\CopyFolderEvent;
use Phlexible\Component\Volume\Event\CreateFileEvent;
use Phlexible\Component\Volume\Event\CreateFileVersionEvent;
use Phlexible\Component\Volume\Event\FileEvent;
use Phlexible\Component\Volume\Event\FileVersionEvent;
use Phlexible\Component\Volume\Event\FolderEvent;
use Phlexible\Component\Volume\Event\MoveFileEvent;
use Phlexible\Component\Volume\Event\MoveFolderEvent;
use Phlexible\Component\Volume\Event\RenameFileEvent;
use Phlexible\Component\Volume\Event\RenameFolderEvent;
use Phlexible\Component\Volume\Event\ReplaceFileEvent;
use Phlexible\Component\Volume\Exception\IOException;
use Phlexible\Component\Volume\FileSource\FileSourceInterface;
use Phlexible\Component\Volume\FileSource\FilesystemFileSource;
use Phlexible\Component\Volume\Model\FileInterface;
use Phlexible\Component\Volume\Model\FileVersionInterface;
use Phlexible\Component\Volume\Model\FolderInterface;
use Phlexible\Component\Volume\Model\FolderIterator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Volume.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Volume implements VolumeInterface, \IteratorAggregate
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var int
     */
    private $quota;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param string                   $id
     * @param string                   $rootDir
     * @param int                      $quota
     * @param DriverInterface          $driver
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct($id, $rootDir, $quota, DriverInterface $driver, EventDispatcherInterface $eventDispatcher)
    {
        $this->id = $id;
        $this->rootDir = $rootDir;
        $this->quota = $quota;
        $this->driver = $driver;
        $this->eventDispatcher = $eventDispatcher;

        $driver->setVolume($this);
    }

    /**
     * @return FolderIterator
     */
    public function getIterator()
    {
        return new FolderIterator($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuota()
    {
        return $this->quota;
    }

    /**
     * {@inheritdoc}
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFeature($feature)
    {
        return in_array($feature, $this->driver->getFeatures());
    }

    /**
     * {@inheritdoc}
     */
    public function findRootFolder()
    {
        return $this->driver->findRootFolder();
    }

    /**
     * {@inheritdoc}
     */
    public function findFolder($id)
    {
        return $this->driver->findFolder($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findFolderByFileId($fileId)
    {
        return $this->driver->findFolderByFileId($fileId);
    }

    /**
     * {@inheritdoc}
     */
    public function findFolderByPath($path)
    {
        return $this->driver->findFolderByPath($path);
    }

    /**
     * {@inheritdoc}
     */
    public function findFoldersByParentFolder(FolderInterface $parentFolder)
    {
        return $this->driver->findFoldersByParentFolder($parentFolder);
    }

    /**
     * {@inheritdoc}
     */
    public function countFoldersByParentFolder(FolderInterface $parentFolder)
    {
        return $this->driver->countFoldersByParentFolder($parentFolder);
    }

    /**
     * {@inheritdoc}
     */
    public function findFile($id)
    {
        return $this->driver->findFile($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findFiles(array $criteria, $order = null, $limit = null, $start = null)
    {
        return $this->driver->findFiles($criteria, $order, $limit, $start);
    }

    /**
     * {@inheritdoc}
     */
    public function countFiles(array $criteria)
    {
        return $this->driver->countFiles($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function findFileByPath($path)
    {
        return $this->driver->findFileByPath($path);
    }

    /**
     * {@inheritdoc}
     */
    public function findFileVersions($id, array $orderBy = null, $limit = null, $start = null)
    {
        return $this->driver->findFileVersionsBy(['file' => $id], $orderBy, $limit, $start);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneFileVersion($id, array $orderBy = null)
    {
        return $this->driver->findFileVersionBy(['file' => $id], $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findFileVersion($id, $version)
    {
        return $this->driver->findFileVersionBy(['file' => $id, 'fileVersion' => $version]);
    }

    /**
     * {@inheritdoc}
     */
    public function findFilesByFolder(
        FolderInterface $folder,
        $order = null,
        $limit = null,
        $start = null,
        $includeHidden = false)
    {
        return $this->driver->findFilesByFolder($folder, $order, $limit, $start, $includeHidden);
    }

    /**
     * {@inheritdoc}
     */
    public function countFilesByFolder(FolderInterface $folder)
    {
        return $this->driver->countFilesByFolder($folder);
    }

    /**
     * {@inheritdoc}
     */
    public function findLatestFiles($limit = 20)
    {
        return $this->driver->findLatestFiles($limit);
    }

    /**
     * {@inheritdoc}
     */
    public function search($query)
    {
        return $this->driver->search($query);
    }

    /**
     * /**
     * {@inheritdoc}
     */
    public function createFile(
        FolderInterface $targetFolder,
        FileSourceInterface $fileSource,
        array $attributes,
        $userId)
    {
        $hash = $this->driver->getHashCalculator()->fromFileSource($fileSource);

        $fileClass = $this->driver->getFileClass();
        $file = new $fileClass();
        /* @var $file FileInterface */
        $file
            ->setVolume($this)
            ->setId(UuidUtil::generate())
            ->setFolder($targetFolder)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($userId);

        $this->applyFile($file, 1, $fileSource->getName(), $fileSource->getMimeType(), $fileSource->getSize(), $hash, $attributes, $file->getCreateUserId(), $file->getCreatedAt());

        $event = new CreateFileEvent($file, $fileSource);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_CREATE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Create file {$file->getName()} failed.");
        }

        $this->driver->createFile($file, $fileSource);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::CREATE_FILE, $event);

        if ($this->hasFeature('versions')) {
            $this->createFileVersion($file, $fileSource, $attributes, $userId);
        }

        return $file;
    }

    private function applyFile(FileInterface $file, $version, $name, $mimeType, $size, $hash, $attributes, $modifyUserId, $modifiedAt)
    {
        $file
            ->setVersion($version)
            ->setName($name)
            ->setMimeType($mimeType)
            ->setSize($size)
            ->setHash($hash)
            ->setAttributes($attributes)
            ->setModifiedAt($modifiedAt)
            ->setModifyUserId($modifyUserId);
    }

    /**
     * {@inheritdoc}
     */
    public function createFileVersion(
        FileInterface $targetFile,
        FileSourceInterface $fileSource,
        array $attributes,
        $userId)
    {
        $hash = $this->driver->getHashCalculator()->fromFileSource($fileSource);

        $highestVersion = $this->findOneFileVersion($targetFile->getId(), ['fileVersion' => 'DESC']);
        if (!$highestVersion) {
            $newVersion = 1;
        } else {
            $newVersion = $highestVersion->getVersion() + 1;
        }

        $fileVersionClass = $this->driver->getFileVersionClass();
        $fileVersion = new $fileVersionClass();
        /* @var $fileVersion FileVersionInterface */

        $fileVersion
            ->setFile($targetFile)
            ->setFileVersion($newVersion)
            ->setName($fileSource->getName())
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($userId)
            ->setModifiedAt($fileVersion->getCreatedAt())
            ->setModifyUserId($fileVersion->getCreateUserId())
            ->setMimeType($fileSource->getMimeType())
            ->setSize($fileSource->getSize())
            ->setHash($hash)
            ->setAttributes($attributes);

        $event = new CreateFileVersionEvent($fileVersion, $fileSource);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_CREATE_FILE_VERSION, $event)->isPropagationStopped()) {
            throw new IOException("Create file version {$fileVersion->getName()} #{$fileVersion->getVersion()} failed.");
        }

        $this->driver->createFileVersion($fileVersion, $fileSource);

        $event = new FileVersionEvent($fileVersion);
        $this->eventDispatcher->dispatch(VolumeEvents::CREATE_FILE_VERSION, $event);

        return $fileVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function activateFileVersion(FileInterface $file, FileVersionInterface $fileVersion, $userId)
    {
        $this->applyFile(
            $file,
            $fileVersion->getVersion(),
            $fileVersion->getName(),
            $fileVersion->getMimeType(),
            $fileVersion->getSize(),
            $fileVersion->getHash(),
            $fileVersion->getAttributes(),
            $userId,
            new \DateTime()
        );

        $event = new FileEvent($file);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_ACTIVATE_FILE_VERSION, $event)->isPropagationStopped()) {
            throw new IOException("Activation of file version {$fileVersion->getName()} #{$fileVersion->getVersion()} failed.");
        }

        $this->driver->updateFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::ACTIVATE_FILE_VERSION, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function replaceFile(
        FileInterface $file,
        FileSourceInterface $fileSource,
        array $attributes,
        $userId)
    {
        $hash = $this->driver->getHashCalculator()->fromFileSource($fileSource);

        $file
            ->setName($fileSource->getName())
            ->setSize($fileSource->getSize())
            ->setHash($hash)
            ->setAttributes($attributes)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $event = new ReplaceFileEvent($file, $fileSource);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_REPLACE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Create file {$file->getName()} failed.");
        }

        $this->driver->replaceFile($file, $fileSource);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::REPLACE_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function renameFile(FileInterface $file, $name, $userId)
    {
        if ($file->getName() === $name) {
            return $file;
        }

        $oldName = $file->getName();
        $file
            ->setName($name)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $this->driver->validateRenameFile($file, $file->getFolder());

        $event = new RenameFileEvent($file, $oldName);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_RENAME_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Rename file {$file->getName()} cancelled.");
        }

        $this->driver->updateFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::RENAME_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function moveFile(FileInterface $file, FolderInterface $targetFolder, $userId)
    {
        if ($file->getFolder()->getId() === $targetFolder->getId()) {
            return $file;
        }

        $file
            ->setFolder($targetFolder)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $this->driver->validateMoveFile($file, $targetFolder);

        $event = new MoveFileEvent($file, $targetFolder);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_MOVE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Move file {$file->getName()} cancelled.");
        }

        $this->driver->updateFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::MOVE_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFile(FileInterface $originalFile, FolderInterface $targetFolder, $userId)
    {
        $file = clone $originalFile;
        $file
            ->setId(UuidUtil::generate())
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($userId)
            ->setModifiedAt($file->getCreatedAt())
            ->setModifyUserId($file->getCreateUserId())
            ->setFolder($targetFolder);

        $this->driver->validateCopyFile($originalFile, $targetFolder);

        $event = new CopyFileEvent($file, $originalFile, $targetFolder);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_COPY_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Copy file {$file->getName()} cancelled.");
        }

        $this->driver->updateFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::COPY_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function checkDeleteFile(FileInterface $file)
    {
        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::CHECK_DELETE_FILE, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile(FileInterface $file, $userId)
    {
        $event = new FileEvent($file);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_DELETE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Delete file {$file->getName()} cancelled.");
        }

        $this->driver->deleteFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::DELETE_FILE, $event);

        return $file;
    }

    /**
     * @param FileInterface $file
     * @param string        $userId
     *
     * @return FileInterface
     */
    public function hideFile(FileInterface $file, $userId)
    {
        $event = new FileEvent($file);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_HIDE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Hide file {$file->getName()} cancelled.");
        }

        $this->driver->hideFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::HIDE_FILE, $event);

        return $file;
    }

    /**
     * @param FileInterface $file
     * @param string        $userId
     *
     * @return FileInterface
     */
    public function showFile(FileInterface $file, $userId)
    {
        $event = new FileEvent($file);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_SHOW_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Show file {$file->getName()} cancelled.");
        }

        $this->driver->showFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::SHOW_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileAttributes(FileInterface $file, array $attributes, $userId)
    {
        $file
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId)
            ->setAttributes($attributes);

        $event = new FileEvent($file);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_SET_FILE_ATTRIBUTES, $event)->isPropagationStopped()) {
            throw new IOException("Set file version attributes {$file->getName()} cancelled.");
        }

        $this->driver->updateFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(VolumeEvents::SET_FILE_ATTRIBUTES, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileVersionAttributes(FileVersionInterface $fileVersion, array $attributes, $userId)
    {
        $fileVersion
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId)
            ->setAttributes($attributes);

        $event = new FileVersionEvent($fileVersion);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_SET_FILE_VERSION_ATTRIBUTES, $event)->isPropagationStopped()) {
            throw new IOException("Set file version attributes for {$fileVersion->getName()} cancelled.");
        }

        $this->driver->updateFileVersion($fileVersion);

        $event = new FileVersionEvent($fileVersion);
        $this->eventDispatcher->dispatch(VolumeEvents::SET_FILE_VERSION_ATTRIBUTES, $event);

        return $fileVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function createFolder(FolderInterface $targetFolder = null, $name, array $attributes, $userId)
    {
        $folderPath = '';
        if ($targetFolder) {
            $folderPath = $name;
            if ($targetFolder->getPath()) {
                $folderPath = rtrim($targetFolder->getPath(), '/').'/'.$folderPath;
            }
        }

        // prepare folder's name and id
        $folderClass = $this->driver->getFolderClass();
        $folder = new $folderClass();
        /* @var $folder FolderInterface */
        $folder
            ->setVolume($this)
            ->setId(UuidUtil::generate())
            ->setName($name)
            ->setParentId($targetFolder ? $targetFolder->getId() : null)
            ->setPath($folderPath)
            ->setAttributes($attributes)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($userId)
            ->setModifiedAt($folder->getCreatedAt())
            ->setModifyUserid($folder->getCreateUserId());

        $this->driver->validateCreateFolder($folder);

        $event = new FolderEvent($folder);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_CREATE_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Create folder {$folder->getName()} cancelled.");
        }

        $this->driver->updateFolder($folder);

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(VolumeEvents::CREATE_FOLDER, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function renameFolder(FolderInterface $folder, $name, $userId)
    {
        $oldPath = $folder->getPath();
        $parentFolder = $this->findFolder($folder->getParentId());
        $newPath = $name;
        if ($parentFolder->getPath()) {
            $newPath = rtrim($parentFolder->getPath(), '/').'/'.$newPath;
        }

        $folder
            ->setName($name)
            ->setPath($newPath)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $this->driver->validateRenameFolder($folder);

        $event = new RenameFolderEvent($folder, $oldPath);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_RENAME_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Rename folder {$folder->getName()} cancelled.");
        }

        $this->driver->renameFolder($folder, $oldPath);

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(VolumeEvents::RENAME_FOLDER, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function moveFolder(FolderInterface $folder, FolderInterface $targetFolder, $userId)
    {
        if ($folder->getParentId() === $targetFolder->getId()) {
            return null;
        }

        if ($folder->getId() === $targetFolder->getId()) {
            return null;
        }

        $oldPath = $folder->getPath();
        $newPath = $folder->getName();
        if ($targetFolder->getPath()) {
            $newPath = rtrim($targetFolder->getPath(), '/').'/'.$newPath;
        }

        $folder
            ->setParentId($targetFolder->getId())
            ->setPath($newPath)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $this->driver->validateMoveFolder($folder);

        $event = new MoveFolderEvent($folder, $targetFolder);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_MOVE_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Move folder {$folder->getName()} cancelled.");
        }

        $this->driver->moveFolder($folder, $oldPath);

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(VolumeEvents::MOVE_FOLDER, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFolder(FolderInterface $folder, FolderInterface $targetFolder, $userId)
    {
        $this->driver->validateCopyFolder($folder, $targetFolder);

        $event = new CopyFolderEvent($folder, $targetFolder);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_COPY_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Move folder {$folder->getName()} cancelled.");
        }

        $copiedFolder = $this->createFolder($targetFolder, $folder->getName().'_copy_'.uniqid(), $folder->getAttributes(), $userId);

        foreach ($this->findFoldersByParentFolder($folder) as $subFolder) {
            $this->copyFolder($subFolder, $copiedFolder, $userId);
        }

        foreach ($this->findFilesByFolder($folder) as $file) {
            $fileSource = new FilesystemFileSource($file->getPhysicalPath(), $file->getMimeType(), $file->getSize());
            $this->createFile($copiedFolder, $fileSource, $file->getAttributes(), $userId);
        }

        $event = new FolderEvent($copiedFolder);
        $this->eventDispatcher->dispatch(VolumeEvents::COPY_FOLDER, $event);

        return $copiedFolder;
    }

    /**
     * {@inheritdoc}
     */
    public function checkDeleteFolder(FolderInterface $folder)
    {
        foreach ($this->findFoldersByParentFolder($folder) as $subFolder) {
            $this->checkDeleteFolder($subFolder);
        }

        foreach ($this->findFilesByFolder($folder) as $file) {
            $this->checkDeleteFile($file);
        }

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(VolumeEvents::CHECK_DELETE_FOLDER, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFolder(FolderInterface $folder, $userId)
    {
        $this->checkDeleteFolder($folder);

        return $this->doDeleteFolder($folder, $userId);
    }

    /**
     * @param FolderInterface $folder
     * @param string          $userId
     *
     * @return FolderInterface
     */
    private function doDeleteFolder(FolderInterface $folder, $userId)
    {
        foreach ($this->findFoldersByParentFolder($folder) as $subFolder) {
            $this->deleteFolder($subFolder, $userId);
        }

        foreach ($this->findFilesByFolder($folder) as $file) {
            $this->deleteFile($file, $userId);
        }

        $event = new FolderEvent($folder);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_DELETE_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Delete folder {$folder->getName()} cancelled.");
        }

        $this->driver->deleteFolder($folder, $userId);

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(VolumeEvents::DELETE_FOLDER, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function setFolderAttributes(FolderInterface $folder, array $attributes, $userId)
    {
        $folder
            ->setAttributes($attributes)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $event = new FolderEvent($folder);
        if ($this->eventDispatcher->dispatch(VolumeEvents::BEFORE_SET_FOLDER_ATTRIBUTES, $event)->isPropagationStopped()) {
            throw new IOException("Move folder {$folder->getName()} cancelled.");
        }

        $this->driver->updateFolder($folder);

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(VolumeEvents::SET_FOLDER_ATTRIBUTES, $event);

        return $folder;
    }
}
