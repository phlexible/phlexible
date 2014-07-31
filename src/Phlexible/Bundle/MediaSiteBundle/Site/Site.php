<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Site;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\CopyFileAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\CopyFolderAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\CreateFileAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\CreateFolderAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\DeleteFileAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\DeleteFolderAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\MoveFileAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\MoveFolderAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\RenameFileAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\RenameFolderAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\ReplaceFileAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\SetFileAttributesAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\SetFolderAttributesAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\DriverInterface;
use Phlexible\Bundle\MediaSiteBundle\Event\AbstractActionEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeCopyFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeCopyFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeCreateFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeCreateFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeDeleteFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeDeleteFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeMoveFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeMoveFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeRenameFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeRenameFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeReplaceFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\CopyFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\CopyFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\CreateFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\CreateFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\DeleteFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\DeleteFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\MoveFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\MoveFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\RenameFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\RenameFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\ReplaceFileEvent;
use Phlexible\Bundle\MediaSiteBundle\FileSource\FileSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderIterator;
use Phlexible\Bundle\MediaSiteBundle\HashCalculator\MessageDigestHashCalculator;
use Phlexible\Bundle\MediaSiteBundle\MediaSiteEvents;
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
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
    private $dispatcher;

    /**
     * @param string                   $id
     * @param string                   $rootDir
     * @param int                      $quota
     * @param DriverInterface          $driver
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct($id, $rootDir, $quota, DriverInterface $driver, EventDispatcherInterface $dispatcher)
    {
        $this->id = $id;
        $this->rootDir = $rootDir;
        $this->quota = $quota;
        $this->driver = $driver;
        $this->dispatcher = $dispatcher;

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
        $hashCalculator = new MessageDigestHashCalculator();

        $action = new CreateFileAction($fileSource, $targetFolder, $hashCalculator, $attributes, new \DateTime(), $userId);

        $event = new BeforeCreateFileEvent($action);
        $this->dispatcher->dispatch(MediaSiteEvents::BEFORE_CREATE_FILE, $event);

        $file = $this->driver->execute($action);

        $event = new CreateFileEvent($action, $file);
        $this->dispatcher->dispatch(MediaSiteEvents::CREATE_FILE, $event);

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
        $hashCalculator = new MessageDigestHashCalculator();

        $action = new ReplaceFileAction($fileSource, $file, $hashCalculator, $attributes, new \DateTime(), $userId);

        $event = new BeforeReplaceFileEvent($action);
        $this->dispatcher->dispatch(MediaSiteEvents::BEFORE_REPLACE_FILE, $event);

        $file = $this->driver->execute($action);

        $event = new ReplaceFileEvent($action, $file);
        $this->dispatcher->dispatch(MediaSiteEvents::REPLACE_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function renameFile(FileInterface $file, $name, $userId)
    {
        $action = new RenameFileAction($file, $name, new \DateTime(), $userId);

        $event = new BeforeRenameFileEvent($action);
        $this->dispatcher->dispatch(MediaSiteEvents::BEFORE_RENAME_FILE, $event);

        $file = $this->driver->execute($action);

        $event = new RenameFileEvent($action, $file);
        $this->dispatcher->dispatch(MediaSiteEvents::RENAME_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function moveFile(FileInterface $file, FolderInterface $targetFolder, $userId)
    {
        $action = new MoveFileAction($file, $targetFolder, new \DateTime, $userId);

        $event = new BeforeMoveFileEvent($action);
        $this->dispatcher->dispatch(MediaSiteEvents::BEFORE_MOVE_FILE, $event);

        $file = $this->driver->execute($action);

        $event = new MoveFileEvent($action, $file);
        $this->dispatcher->dispatch(MediaSiteEvents::MOVE_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFile(FileInterface $file, FolderInterface $targetFolder, $userId)
    {
        $action = new CopyFileAction($file, $targetFolder, new \DateTime(), $userId);

        $event = new BeforeCopyFileEvent($action);
        $this->dispatcher->dispatch(MediaSiteEvents::BEFORE_COPY_FILE, $event);

        $file = $this->driver->execute($action);

        $event = new CopyFileEvent($action, $file);
        $this->dispatcher->dispatch(MediaSiteEvents::COPY_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile(FileInterface $file, $userId)
    {
        $action = new DeleteFileAction($file, new \DateTime(), $userId);

        $event = new BeforeDeleteFileEvent($action);
        $this->dispatcher->dispatch(MediaSiteEvents::BEFORE_DELETE_FILE, $event);
        if ($event->isPropagationStopped()) {
            throw new \Exception('Delete cancelled');
        }

        $file = $this->driver->execute($action);

        $event = new DeleteFileEvent($action, $file);
        $this->dispatcher->dispatch(MediaSiteEvents::DELETE_FILE, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileAttributes(FileInterface $file, AttributeBag $attributes, $userId)
    {
        $action = new SetFileAttributesAction($file, $attributes, new \DateTime(), $userId);

        $event = new AbstractActionEvent($action);
        $this->dispatcher->dispatch(MediaSiteEvents::BEFORE_SET_FILE_ATTRIBUTES, $event);

        $file = $this->driver->execute($action);

        $event = new AbstractActionEvent($action, $file);
        $this->dispatcher->dispatch(MediaSiteEvents::SET_FILE_ATTRIBUTES, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function createFolder(FolderInterface $targetFolder, $name, AttributeBag $attributes, $userId)
    {
        $action = new CreateFolderAction($name, $targetFolder, $attributes, new \DateTime(), $userId);

        $event = new BeforeCreateFolderEvent($action);
        $this->dispatcher->dispatch(MediaSiteEvents::BEFORE_CREATE_FOLDER, $event);

        $folder = $this->driver->execute($action);

        $event = new CreateFolderEvent($action, $folder);
        $this->dispatcher->dispatch(MediaSiteEvents::CREATE_FOLDER, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function renameFolder(FolderInterface $folder, $name, $userId)
    {
        $action = new RenameFolderAction($folder, $name, new \DateTime(), $userId);

        $event = new BeforeRenameFolderEvent($action);
        $this->dispatcher->dispatch(MediaSiteEvents::BEFORE_RENAME_FOLDER, $event);

        $folder = $this->driver->execute($action);

        $event = new RenameFolderEvent($action, $folder);
        $this->dispatcher->dispatch(MediaSiteEvents::RENAME_FOLDER, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function moveFolder(FolderInterface $folder, FolderInterface $targetFolder, $userId)
    {
        $action = new MoveFolderAction($folder, $targetFolder, new \DateTime(), $userId);

        $event = new BeforeMoveFolderEvent($action);
        $this->dispatcher->dispatch(MediaSiteEvents::BEFORE_MOVE_FOLDER, $event);

        $folder = $this->driver->execute($action);

        $event = new MoveFolderEvent($action, $folder);
        $this->dispatcher->dispatch(MediaSiteEvents::MOVE_FOLDER, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFolder(FolderInterface $folder, FolderInterface $targetFolder, $userId)
    {
        $action = new CopyFolderAction($folder, $targetFolder, new \DateTime(), $userId);

        $event = new BeforeCopyFolderEvent($action);
        $this->dispatcher->dispatch(MediaSiteEvents::BEFORE_COPY_FOLDER, $event);

        $file = $this->driver->execute($action);

        $event = new CopyFolderEvent($action, $folder);
        $this->dispatcher->dispatch(MediaSiteEvents::COPY_FOLDER, $event);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFolder(FolderInterface $folder, $userId)
    {
        foreach ($this->findFoldersByParentFolder($folder) as $subFolder) {
            $this->deleteFolder($subFolder, $userId);
        }

        foreach ($this->findFilesByFolder($folder) as $file) {
            $this->deleteFile($file, $userId);
        }

        $action = new DeleteFolderAction($folder, new \DateTime(), $userId);

        $event = new BeforeDeleteFolderEvent($action);
        $this->dispatcher->dispatch(MediaSiteEvents::BEFORE_DELETE_FOLDER, $event);
        if ($event->isPropagationStopped()) {
            throw new \Exception('Delete cancelled');
        }

        $folder = $this->driver->execute($action);

        $event = new DeleteFolderEvent($action, $folder);
        $this->dispatcher->dispatch(MediaSiteEvents::DELETE_FOLDER, $event);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function setFolderAttributes(FolderInterface $folder, AttributeBag $attributes, $userId)
    {
        $action = new SetFolderAttributesAction($folder, $attributes, new \DateTime, $userId);

        $event = new AbstractActionEvent($action);
        $this->dispatcher->dispatch(MediaSiteEvents::BEFORE_SET_FOLDER_ATTRIBUTES, $event);

        $folder = $this->driver->execute($action);

        $event = new AbstractActionEvent($action, $folder);
        $this->dispatcher->dispatch(MediaSiteEvents::SET_FOLDER_ATTRIBUTES, $event);

        return $folder;
    }
}
