<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Site;

use Phlexible\Bundle\MediaSiteBundle\Driver\DriverInterface;
use Phlexible\Bundle\MediaSiteBundle\FileSource\FileSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderIterator;
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;

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
     * @param string          $id
     * @param string          $rootDir
     * @param int             $quota
     * @param DriverInterface $driver
     */
    public function __construct($id, $rootDir, $quota, DriverInterface $driver)
    {
        $this->id = $id;
        $this->rootDir = $rootDir;
        $this->quota = $quota;
        $this->driver = $driver;

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
        $file = $this->driver->createFile($targetFolder, $fileSource, $attributes, $userId);

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
        $file = $this->driver->replaceFile($file, $fileSource, $attributes, $userId);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function renameFile(FileInterface $file, $name, $userId)
    {
        $file = $this->driver->renameFile($file, $name, $userId);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function moveFile(FileInterface $file, FolderInterface $targetFolder, $userId)
    {
        $file = $this->driver->moveFile($file, $targetFolder, $userId);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFile(FileInterface $file, FolderInterface $targetFolder, $userId)
    {
        $file = $this->driver->copyFile($file, $targetFolder, $userId);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile(FileInterface $file, $userId)
    {
        $file = $this->driver->deleteFile($file, $userId);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileAttributes(FileInterface $file, AttributeBag $attributes, $userId)
    {
        $file = $this->driver->setFileAttributes($file, $attributes, $userId);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function createFolder(FolderInterface $targetFolder, $name, AttributeBag $attributes, $userId)
    {
        $folder = $this->driver->createFolder($targetFolder, $name, $attributes, $userId);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function renameFolder(FolderInterface $folder, $name, $userId)
    {
        $folder = $this->driver->renameFolder($folder, $name, $userId);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function moveFolder(FolderInterface $folder, FolderInterface $targetFolder, $userId)
    {
        $folder = $this->driver->moveFolder($folder, $targetFolder, $userId);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFolder(FolderInterface $folder, FolderInterface $targetFolder, $userId)
    {
        $file = $this->driver->copyFolder($folder, $targetFolder, $userId);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFolder(FolderInterface $folder, $userId)
    {
        $folder = $this->driver->deleteFolder($folder, $userId);

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function setFolderAttributes(FolderInterface $folder, AttributeBag $attributes, $userId)
    {
        $folder = $this->driver->setFolderAttributes($folder, $attributes, $userId);

        return $folder;
    }
}
