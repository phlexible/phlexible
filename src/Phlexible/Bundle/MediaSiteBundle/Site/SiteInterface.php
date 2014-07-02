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
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\SetFileAttributesAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\SetFolderAttributesAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\DriverInterface;
use Phlexible\Bundle\MediaSiteBundle\Driver\FindInterface;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\FileSource\FileSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderInterface;

/**
 * Media site interface
 * Represents a complete set of classes used to get a virtual set of folders and files
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SiteInterface extends FindInterface
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
     * @param string $feature
     *
     * @return bool
     */
    public function hasFeature($feature);

    /**
     * @return \Media_SiteDb_Folder_Peer
     *
     * @deprecated
     */
    public function getFolderPeer();

    /**
     * @return \Media_SiteDb_File_Peer
     *
     * @deprecated
     */
    public function getFilePeer();

    /**
     * @param FolderInterface $targetFolder
     * @param string          $name
     * @param string          $userId
     *
     * @return CreateFolderAction
     */
    public function createFolder(FolderInterface $targetFolder, $name, $userId);

    /**
     * @param FolderInterface $folder
     * @param string          $name
     * @param string          $userId
     *
     * @return RenameFolderAction
     */
    public function renameFolder(FolderInterface $folder, $name, $userId);

    /**
     * @param FolderInterface $folder
     * @param FolderInterface $targetFolder
     * @param string          $userId
     *
     * @return MoveFolderAction
     */
    public function moveFolder(FolderInterface $folder, FolderInterface $targetFolder, $userId);

    /**
     * @param FolderInterface $folder
     * @param FolderInterface $targetFolder
     * @param string          $userId
     *
     * @return CopyFolderAction
     */
    public function copyFolder(FolderInterface $folder, FolderInterface $targetFolder, $userId);

    /**
     * @param FolderInterface $folder
     * @param string          $userId
     *
     * @return DeleteFolderAction
     */
    public function deleteFolder(FolderInterface $folder, $userId);

    /**
     * @param FolderInterface $folder
     * @param array           $attributes
     *
     * @return SetFolderAttributesAction
     */
    public function setFolderAttributes(FolderInterface $folder, array $attributes);

    /**
     * @param FolderInterface     $targetFolder
     * @param FileSourceInterface $fileSource
     * @param string              $userId
     * @param array               $attributes
     *
     * @return CreateFileAction
     */
    public function createFile(FolderInterface $targetFolder, FileSourceInterface $fileSource, $userId, array $attributes = array());

    /**
     * @param FileInterface $file
     * @param string        $name
     * @param string        $userId
     *
     * @return RenameFileAction
     */
    public function renameFile(FileInterface $file, $name, $userId);

    /**
     * @param FileInterface   $file
     * @param FolderInterface $targetFolder
     * @param string          $userId
     *
     * @return MoveFileAction
     */
    public function moveFile(FileInterface $file, FolderInterface $targetFolder, $userId);

    /**
     * @param FileInterface   $file
     * @param FolderInterface $targetFolder
     * @param string          $userId
     *
     * @return CopyFileAction
     */
    public function copyFile(FileInterface $file, FolderInterface $targetFolder, $userId);

    /**
     * @param FileInterface $file
     * @param string        $userId
     *
     * @return DeleteFileAction
     */
    public function deleteFile(FileInterface $file, $userId);

    /**
     * @param FileInterface $file
     * @param array         $attributes
     *
     * @return SetFileAttributesAction
     */
    public function setFileAttributes(FileInterface $file, array $attributes);
}
