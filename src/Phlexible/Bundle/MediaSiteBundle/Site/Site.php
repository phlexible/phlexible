<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Site;

use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\MediaSiteBundle\Driver\DriverInterface;
use Phlexible\Bundle\MediaSiteBundle\Event\CopyFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\CopyFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\CreateFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\FileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\FolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\MoveFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\MoveFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\RenameFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\RenameFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\ReplaceFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Exception\IOException;
use Phlexible\Bundle\MediaSiteBundle\FileSource\FileSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\FileSource\FilesystemFileSource;
use Phlexible\Bundle\MediaSiteBundle\MediaSiteEvents;
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderIterator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Site
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Site implements SiteInterface, \IteratorAggregate
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

        $driver->setSite($this);
    }

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
     * {@inheritdoc}
     */
    public function hasFeature($feature)
    {
        return in_array($feature, $this->driver->getFeatures());
    }

    /**
     * {@inheritdoc}
     */
    public function getFilePeer()
    {
        throw new \RuntimeException('Removed.');
    }

    /**
     * {@inheritdoc}
     */
    public function getFolderPeer()
    {
        throw new \RuntimeException('Removed.');
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
    public function findFile($id, $version = 1)
    {
        return $this->driver->findFile($id, $version);
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
    public function findFileByPath($path, $version = 1)
    {
        return $this->driver->findFileByPath($path, $version);
    }

    /**
     * {@inheritdoc}
     */
    public function findFileVersions($id)
    {
        return $this->driver->findFileVersions($id);
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
        AttributeBag $attributes,
        $userId)
    {
        $hash = $this->driver->getHashCalculator()->fromFileSource($fileSource);

        // prepare folder's name and id
        $fileClass = $this->driver->getFileClass();
        $file = new $fileClass();
        /* @var $file FileInterface */
        $file
            ->setSite($this)
            ->setId(Uuid::generate())
            ->setFolder($targetFolder)
            ->setName($fileSource->getName())
            ->setCreatedAt(new \DateTime())
            ->setCreateUserid($userId)
            ->setModifiedAt($file->getCreatedAt())
            ->setModifyUserid($file->getCreateUserId())
            ->setMimeType($fileSource->getMimeType())
            ->setSize($fileSource->getSize())
            ->setHash($hash)
            ->setAttributes($attributes);

        $event = new CreateFileEvent($file, $fileSource);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_CREATE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Create file {$file->getName()} failed.");
        }

        $this->driver->createFile($file, $fileSource);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(MediaSiteEvents::CREATE_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function replaceFile(
        FileInterface $file,
        FileSourceInterface $fileSource,
        AttributeBag $attributes,
        $userId)
    {
        $hash = $this->driver->getHashCalculator()->fromFileSource($fileSource);

        $file
            ->setName($fileSource->getName())
            ->setSize($fileSource->getSize())
            ->setHash($hash)
            ->setAttributes($attributes)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserid($userId);

        $event = new ReplaceFileEvent($file, $fileSource);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_REPLACE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Create file {$file->getName()} failed.");
        }

        $this->driver->replaceFile($file, $fileSource);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(MediaSiteEvents::REPLACE_FILE, $event);

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

        $this->driver->validateRenameFile($file);

        $event = new RenameFileEvent($file, $oldName);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_RENAME_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Rename file {$file->getName()} cancelled.");
        }

        $this->driver->updateFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(MediaSiteEvents::RENAME_FILE, $event);

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
            ->setFolderId($targetFolder->getId())
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $this->driver->validateMoveFile($file);

        $event = new MoveFileEvent($file, $targetFolder);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_MOVE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Move file {$file->getName()} cancelled.");
        }

        $this->driver->updateFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(MediaSiteEvents::MOVE_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFile(FileInterface $originalFile, FolderInterface $targetFolder, $userId)
    {
        $file = clone $originalFile;
        $file
            ->setId(Uuid::generate())
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($userId)
            ->setModifiedAt($file->getCreatedAt())
            ->setModifyUserId($file->getCreateUserId())
            ->setFolderId($targetFolder->getId());

        $this->driver->validateCopyFile($file);

        $event = new CopyFileEvent($file, $originalFile, $targetFolder);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_COPY_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Copy file {$file->getName()} cancelled.");
        }

        $this->driver->updateFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(MediaSiteEvents::COPY_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function checkDeleteFile(FileInterface $file)
    {
        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(MediaSiteEvents::CHECK_DELETE_FILE, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile(FileInterface $file, $userId)
    {
        $event = new FileEvent($file);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_DELETE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Delete file {$file->getName()} cancelled.");
        }

        $this->driver->deleteFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(MediaSiteEvents::DELETE_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileAttributes(FileInterface $file, AttributeBag $attributes, $userId)
    {
        $file
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId)
            ->setAttributes($attributes);

        $event = new FileEvent($file);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_SET_FILE_ATTRIBUTES, $event)->isPropagationStopped()) {
            throw new IOException("Delete file {$file->getName()} cancelled.");
        }

        $this->driver->updateFile($file);

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(MediaSiteEvents::SET_FILE_ATTRIBUTES, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function createFolder(FolderInterface $targetFolder = null, $name, AttributeBag $attributes, $userId)
    {
        $folderPath = '';
        if ($targetFolder) {
            $folderPath = $name;
            if ($targetFolder->getPath()) {
                $folderPath = rtrim($targetFolder->getPath(), '/') . '/' . $folderPath;
            }
        }

        // prepare folder's name and id
        $folderClass = $this->driver->getFolderClass();
        $folder = new $folderClass();
        /* @var $folder FolderInterface */
        $folder
            ->setSite($this)
            ->setId(Uuid::generate())
            ->setName($name)
            ->setParentId($targetFolder ? $targetFolder->getId() : null)
            ->setPath($folderPath)
            ->setAttributes($attributes)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserid($userId)
            ->setModifiedAt($folder->getCreatedAt())
            ->setModifyUserid($folder->getCreateUserId());

        $this->driver->validateCreateFolder($folder);

        $event = new FolderEvent($folder);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_CREATE_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Create folder {$folder->getName()} cancelled.");
        }

        $this->driver->updateFolder($folder);

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(MediaSiteEvents::CREATE_FOLDER, $event);

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
            $newPath = rtrim($parentFolder->getPath(), '/') . '/' . $newPath;
        }

        $folder
            ->setName($name)
            ->setPath($newPath)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $this->driver->validateRenameFolder($folder);

        $event = new RenameFolderEvent($folder, $oldPath);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_RENAME_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Rename folder {$folder->getName()} cancelled.");
        }

        $this->driver->renameFolder($folder, $oldPath);

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(MediaSiteEvents::RENAME_FOLDER, $event);

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
            $newPath = rtrim($targetFolder->getPath(), '/') . '/' . $newPath;
        }

        $folder
            ->setParentId($targetFolder->getId())
            ->setPath($newPath)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $this->driver->validateMoveFolder($folder);

        $event = new MoveFolderEvent($folder, $targetFolder);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_MOVE_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Move folder {$folder->getName()} cancelled.");
        }

        $this->driver->moveFolder($folder, $oldPath);

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(MediaSiteEvents::MOVE_FOLDER, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFolder(FolderInterface $folder, FolderInterface $targetFolder, $userId)
    {
        $this->driver->validateCopyFolder($folder, $targetFolder);

        $event = new CopyFolderEvent($folder, $targetFolder);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_COPY_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Move folder {$folder->getName()} cancelled.");
        }

        $copiedFolder = $this->createFolder($targetFolder, $folder->getName() . '_copy_' . uniqid(), $folder->getAttributes(), $userId);

        foreach ($this->findFoldersByParentFolder($folder) as $subFolder) {
            $this->copyFolder($subFolder, $copiedFolder, $userId);
        }

        foreach ($this->findFilesByFolder($folder) as $file) {
            $fileSource = new FilesystemFileSource($file->getPhysicalPath(), $file->getMimeType(), $file->getSize());
            $this->createFile($copiedFolder, $fileSource, $file->getAttributes(), $userId);
        }

        $event = new FolderEvent($copiedFolder);
        $this->eventDispatcher->dispatch(MediaSiteEvents::COPY_FOLDER, $event);

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
        $this->eventDispatcher->dispatch(MediaSiteEvents::CHECK_DELETE_FOLDER, $event);
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
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_DELETE_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Delete folder {$folder->getName()} cancelled.");
        }

        $this->driver->deleteFolder($folder, $userId);

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(MediaSiteEvents::DELETE_FOLDER, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function setFolderAttributes(FolderInterface $folder, AttributeBag $attributes, $userId)
    {
        $folder
            ->setAttributes($attributes)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $event = new FolderEvent($folder);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_SET_FOLDER_ATTRIBUTES, $event)->isPropagationStopped()) {
            throw new IOException("Move folder {$folder->getName()} cancelled.");
        }

        $this->driver->updateFolder($folder);

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(MediaSiteEvents::SET_FOLDER_ATTRIBUTES, $event);

        return $folder;
    }
}
