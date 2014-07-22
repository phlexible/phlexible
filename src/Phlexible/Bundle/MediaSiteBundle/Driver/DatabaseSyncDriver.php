<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver;

use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\MediaSiteBundle\Exception;
use Phlexible\Bundle\MediaSiteBundle\Exception\AlreadyExistsException;
use Phlexible\Bundle\MediaSiteBundle\Exception\IOException;
use Phlexible\Bundle\MediaSiteBundle\Exception\NotFoundException;
use Phlexible\Bundle\MediaSiteBundle\Exception\NotWritableException;
use Phlexible\Bundle\MediaSiteBundle\FileSource\FileSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\FileSource\StreamSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\HashCalculator\HashCalculator;
use Phlexible\Bundle\MediaSiteBundle\Model\File;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\Folder;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;
use Symfony\Component\Filesystem\Filesystem;
use Zend_Db_Adapter_Abstract as Connection;

/**
 * Database driver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DatabaseSyncDriver implements DriverInterface
{
    /**
     * @var Connection
     */
    private $db;

    /**
     * @var SiteInterface
     */
    private $site;

    /**
     * @var string
     */
    private $folderTable;

    /**
     * @var string
     */
    private $fileTable;

    /**
     * @param Connection $connection
     * @param string     $name
     */
    public function __construct(Connection $connection, $name)
    {
        $this->db = $connection;

        $this->folderTable = sprintf('%smedia_site_folder', $connection->prefix, $name);
        $this->fileTable = sprintf('%smedia_site_file', $connection->prefix, $name);
    }

    /**
     * @return array
     */
    public function getFeatures()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function setSite(SiteInterface $site)
    {
        $this->site = $site;
    }

    /**
     * {@inheritdoc}
     */
    public function findFolder($id)
    {
        if ($id === -1) {
            return $this->findRootFolder();
        }

        $select = $this->db
            ->select()
            ->from($this->folderTable)
            ->where('id = ?', $id);

        $row = $this->db->fetchRow($select);

        if (!$row) {
            throw new NotFoundException("Folder $id not found.");
        }

        return $this->mapFolderRow($row);
    }

    /**
     * {@inheritdoc}
     */
    public function findRootFolder()
    {
        $select = $this->db
            ->select()
            ->from($this->folderTable)
            ->where('site_id = ?', $this->site->getId())
            ->where('parent_id IS NULL OR parent_id = ""');

        $row = $this->db->fetchRow($select);

        if (!$row) {
            throw new NotFoundException("Root folder not found.");
        }

        return $this->mapFolderRow($row);
    }

    /**
     * {@inheritdoc}
     */
    public function findFolderByPath($path)
    {
        $path = ltrim($path, '/');

        $select = $this->db
            ->select()
            ->from($this->folderTable)
            ->where('site_id = ?', $this->site->getId())
            ->where('path = ?', $path);

        $row = $this->db->fetchRow($select);

        if (!$row) {
            return null;
        }

        return $this->mapFolderRow($row);
    }

    /**
     * {@inheritdoc}
     */
    public function findFoldersByParentFolder(FolderInterface $parentFolder)
    {
        $select = $this->db
            ->select()
            ->from($this->folderTable)
            ->where('parent_id = ?', $parentFolder->getId());

        $rows = $this->db->fetchAll($select);

        return $this->mapFolderRows($rows);
    }

    /**
     * {@inheritdoc}
     */
    public function countFoldersByParentFolder(FolderInterface $parentFolder)
    {
        $select = $this->db
            ->select()
            ->from($this->folderTable, $this->db->fn->expr('COUNT(id)'))
            ->where('parent_id = ?', $parentFolder->getId());

        return $this->db->fetchOne($select);
    }

    /**
     * {@inheritdoc}
     */
    public function findFolderByFileId($fileId)
    {
        $select = $this->db->select()
            ->from(array('fo' => $this->folderTable))
            ->join(
                array('fi' => $this->fileTable),
                'fo.id = fi.folder_id AND fi.id = ' . $this->db->quote($fileId),
                array()
            );

        $row = $this->db->fetchRow($select);

        if (!$row) {
            throw new NotFoundException("Folder for file $fileId not found.");
        }

        return $this->mapFolderRow($row);
    }

    /**
     * {@inheritdoc}
     */
    public function findFile($id, $version = 1)
    {
        $select = $this->db
            ->select()
            ->from(array('fi' => $this->fileTable))
            ->join(array('fo' => $this->folderTable), 'fo.id = fi.folder_id', array('path'))
            ->where('fi.id = ?', $id)
            ->where('fi.version = ?', $version);

        $row = $this->db->fetchRow($select);

        if (!$row) {
            throw new NotFoundException("File $id version $version not found.");
        }

        return $this->mapFileRow($row);
    }

    /**
     * {@inheritdoc}
     */
    public function findFileByPath($path, $version = 1)
    {
        $name = basename($path);
        $dir = trim(dirname($path), '/');

        $select = $this->db
            ->select()
            ->from(array('fi' => $this->fileTable))
            ->join(array('fo' => $this->folderTable), 'fo.id = fi.folder_id', array('path'))
            ->where('fo.path = ?', $dir)
            ->where('fi.name = ?', $name)
            ->where('version = ?', $version);

        $row = $this->db->fetchRow($select);

        if (!$row) {
            return null;
        }

        return $this->mapFileRow($row);
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
        $select = $this->db
            ->select()
            ->from(array('fi' => $this->fileTable))
            ->join(array('fo' => $this->folderTable), 'fo.id = fi.folder_id', array('path'))
            ->where('folder_id = ?', $folder->getId());

        if ($order) {
            $select->order($order);
        }

        if ($limit) {
            $select->limit($limit, $start);
        }

        if (!$includeHidden) {
            $select->where('fi.hidden = 0');
        }

        $rows = $this->db->fetchAll($select);

        return $this->mapFileRows($rows);
    }

    /**
     * {@inheritdoc}
     */
    public function countFilesByFolder(FolderInterface $folder)
    {
        $select = $this->db
            ->select()
            ->from($this->fileTable, $this->db->fn->expr('COUNT(id)'))
            ->where('folder_id = ?', $folder->getId());

        return $this->db->fetchOne($select);
    }

    public function findLatestFiles($limit = 20)
    {
        $select = $this->db
            ->select()
            ->from(array('fi' => $this->fileTable))
            ->join(array('fo' => $this->folderTable), 'fo.id = fi.folder_id', array('path'))
            ->order('create_time DESC')
            ->limit($limit);

        $rows = $this->db->fetchAll($select);

        return $this->mapFileRows($rows);
    }

    public function createFolder(FolderInterface $targetFolder, $name, $userId)
    {
        $filesystem = new Filesystem();

        $folderPath = trim($targetFolder->getPath() . '/' . $name, '/');

        // prepare folder's name and id
        $folder = new Folder();
        $folder
            ->setId(Uuid::generate())
            ->setName($name)
            ->setParentId($targetFolder->getId())
            ->setPath($folderPath)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserid($userId)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserid($userId);

        $siteId = $this->site->getId();
        $path = $this->site->getRootDir() . $folder->getPath();

        $select = $this->db
            ->select()
            ->from($this->folderTable, 'id')
            ->where('path = ?', $folder->getPath())
            ->where('site_id = ?', $siteId);

        if ($this->db->fetchOne($select)) {
            throw new AlreadyExistsException("Create folder {$folder->getName()} failed.");
        }

        if ($filesystem->exists($path)) {
            throw new AlreadyExistsException("Create folder {$folder->getName()} failed");
        }

        $filesystem->mkdir($path, 0777);

        try {
            $this->db->insert(
                $this->folderTable,
                array(
                    'id'             => $folder->getId(),
                    'parent_id'      => $folder->getParentId(),
                    'site_id'        => $siteId,
                    'name'           => $folder->getName(),
                    'path'           => $folder->getPath(),
                    'create_user_id' => $folder->getCreateUserId(),
                    'create_time'    => $folder->getCreatedAt()->format('Y-m-d H:i:s'),
                    'modify_user_id' => $folder->getModifyUserId(),
                    'modify_time'    => $folder->getModifiedAt()->format('Y-m-d H:i:s'),
                )
            );
        } catch (\Exception $e) {
            $filesystem->remove($path);

            throw new IOException("Create folder $name failed", 0, $e);
        }

        return $folder;
    }

    public function renameFolder(FolderInterface $folder, $name, $userId)
    {
        if ($folder->getName() === $name) {
            return $folder;
        }

        $filesystem = new Filesystem();

        $folder
            ->setName($name)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        if (!$folder->isRoot()) {
            $parentFolder = $this->findFolder($folder->getParentId());
            $oldPath = $folder->getPath();
            $newPath = ltrim($parentFolder->getPath() . '/' . $name, '/');
            $oldPhysicalPath = $this->site->getRootDir() . '/' . $oldPath;
            $newPhysicalPath = $this->site->getRootDir() . '/' . $newPath;

            if ($filesystem->exists($newPhysicalPath)) {
                throw new AlreadyExistsException('Can\'t rename folder to "' . $newPhysicalPath . '": already exists');
            }

            $folder->setPath($newPath);
        }

        try {
            $this->db->update(
                $this->folderTable,
                array(
                    'name'           => $folder->getName(),
                    'modify_user_id' => $folder->getModifyUserId(),
                    'modify_time'    => $folder->getModifiedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'id = ?' => $folder->getId()
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Rename folder to $name failed", 0, $e);
        }

        if (!empty($newPath) && !empty($oldPath)) {
            $filesystem->rename($oldPhysicalPath, $newPhysicalPath);

            try {
                $this->db->update(
                    $this->folderTable,
                    array(
                        'path' => $this->db->fn->expr(
                                'REPLACE(path, ' . $this->db->quote($oldPath) . ', ' . $this->db->quote($newPath) . ')'
                            )
                    ),
                    array(
                        'site_id = ?' => $this->site->getId(),
                        'path LIKE ?' => $oldPath . '%',
                    )
                );
            } catch (\Exception $e) {
                throw new IOException("Can't rename folder from $oldPath to $newPath failed", 0, $e);
            }
        }

        return $folder;
    }

    public function copyFolder(FolderInterface $folder, FolderInterface $targetFolder, $userId)
    {

    }

    public function moveFolder(FolderInterface $folder, FolderInterface $targetFolder, $userId)
    {
        if ($folder->getParentId() === $targetFolder->getId()) {
            return $folder;
        }

        if ($folder->getId() === $targetFolder->getId()) {
            return $folder;
        }

        $filesystem = new Filesystem();

        $parentFolder = $this->findFolder($folder->getParentId());
        $oldPath = $folder->getPath();
        $newPath = trim($targetFolder->getPath() . '/' . $folder->getName(), '/');
        $oldPhysicalPath = $this->site->getRootDir() . '/' . $oldPath;
        $newPhysicalPath = $this->site->getRootDir() . '/' . $newPath;

        $folder
            ->setParentId($targetFolder->getId())
            ->setPath($newPath)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $filesystem->rename($oldPhysicalPath, $newPhysicalPath);

        try {
            $this->db->update(
                $this->folderTable,
                array(
                    'path'           => $folder->getPath(),
                    'parent_id'      => $targetFolder->getId(),
                    'modify_user_id' => $folder->getModifyUserId(),
                    'modify_time'    => $folder->getModifiedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'id = ?' => $folder->getId()
                )
            );

            if ($parentFolder->isRoot()) {
                $pathExpression = $this->db->fn->expr(
                    'CONCAT(' . $this->db->quote($targetFolder->getPath()) . ', path)'
                );
            } else {
                $pathExpression = $this->db->fn->expr(
                    'REPLACE(path, ' . $this->db->quote($parentFolder->getPath()) . ', ' . $this->db->quote(
                        $targetFolder->getPath()
                    ) . ')'
                );
            }

            $this->db->update(
                $this->folderTable,
                array(
                    'path' => $pathExpression
                ),
                array(
                    'site_id = ?' => $this->site->getId(),
                    'path LIKE ?' => $oldPath . '%',
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Move folder from $oldPath to $newPath failed.", 0, $e);
        }

        return $folder;
    }

    public function deleteFolder(FolderInterface $folder, $userId)
    {
        $this->deletePhysicalFolder($folder, $userId);

        try {
            $this->db->delete(
                $this->folderTable,
                array(
                    'site_id = ?' => $this->site->getId(),
                    'path LIKE ?' => $folder->getPath() . '%'
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Delete folder failed.", 0, $e);
        }

        return $folder;
    }

    public function createFile(FolderInterface $targetFolder, FileSourceInterface $fileSource, $userId)
    {
        $filesystem = new Filesystem();
        $hashCalculator = new HashCalculator();

        $name = $fileSource->getName();

        // prepare folder's name and id
        $file = new File();
        $file
            ->setId(Uuid::generate())
            ->setFolderId($targetFolder->getId())
            ->setName($name)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserid($userId)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserid($userId);

        $path = $this->site->getRootDir() . trim($targetFolder->getPath() . '/' . $name, '/');

        $select = $this->db
            ->select()
            ->from($this->fileTable, 'id')
            ->where('name = ?', $file->getName())
            ->where('folder_id = ?', $file->getFolderId());

        if ($this->db->fetchOne($select)) {
            throw new AlreadyExistsException("Create file {$file->getName()} failed, already exists in database.");
        }

        if ($filesystem->exists($path)) {
            throw new AlreadyExistsException("Create file {$file->getName()}, already exists at $path");
        }

        if ($fileSource instanceof StreamSourceInterface) {
            $stream = $fileSource->getStream();
            rewind($stream);
            $fd = fopen($path, 'w+');
            stream_copy_to_stream($stream, $fd);
            fclose($fd);
            fclose($stream);
            $file->setMimeType($fileSource->getMimeType());
        } elseif ($fileSource instanceof PathSourceInterface) {
            $filesystem->copy($fileSource->getPath(), $path);
            $file->setMimeType($fileSource->getMimeType());
        } else {
            $filesystem->touch($path);
            $file->setMimeType($fileSource->getMimeType());
        }

        $file
            #->setMimeType('application/x-empty')
            ->setSize(filesize($path))
            ->setHash($hashCalculator->fromPath($path));

        try {
            $this->db->insert(
                $this->fileTable,
                array(
                    'id'             => $file->getId(),
                    'folder_id'      => $file->getFolderId(),
                    'name'           => $file->getName(),
                    'mime_type'      => $file->getMimeType(),
                    'size'           => $file->getSize(),
                    'hash'           => $file->getHash(),
                    'create_user_id' => $file->getCreateUserId(),
                    'create_time'    => $file->getCreatedAt()->format('Y-m-d H:i:s'),
                    'modify_user_id' => $file->getModifyUserId(),
                    'modify_time'    => $file->getModifiedAt()->format('Y-m-d H:i:s'),
                )
            );
        } catch (\Exception $e) {
            $filesystem->remove($path);

            throw new IOException("Create file $name failed.", 0, $e);
        }

        return $file;
    }

    public function renameFile(FileInterface $file, $name, $userId)
    {
        if ($file->getName() === $name) {
            return $file;
        }

        $filesystem = new Filesystem();

        $folder = $this->findFolderByFileId($file->getId());
        $oldPhysicalPath = $this->site->getRootDir() . $folder->getPath() . '/' . $file->getName();
        $newPhysicalPath = $this->site->getRootDir() . $folder->getPath() . '/' . $name;
        $oldName = $file->getName();

        if ($filesystem->exists($newPhysicalPath)) {
            throw new AlreadyExistsException("Rename file from $oldName to $name failed.");
        }

        $file
            ->setName($name)
            ->setModifiedAt(new \DateTime)
            ->setModifyUserId($userId);

        $filesystem->rename($oldPhysicalPath, $newPhysicalPath);

        try {
            $this->db->update(
                $this->fileTable,
                array(
                    'name'           => $file->getName(),
                    'modify_user_id' => $file->getModifyUserId(),
                    'modify_time'    => $file->getModifiedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'id = ?' => $file->getId()
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Rename file from $oldName to $name failed.", 0, $e);
        }
    }

    public function moveFile(FileInterface $file, FolderInterface $targetFolder, $userId)
    {
        if ($file->getFolderId() === $targetFolder->getId()) {
            return $file;
        }

        $filesystem = new Filesystem();

        $sourceFolder = $this->findFolderByFileId($file->getId());
        $oldPhysicalPath = $this->site->getRootDir() . $sourceFolder->getPath() . '/' . $file->getName();
        $newPhysicalPath = $this->site->getRootDir() . $targetFolder->getPath() . '/' . $file->getName();

        if ($filesystem->exists($newPhysicalPath)) {
            throw new AlreadyExistsException("Move file from $oldPhysicalPath to $newPhysicalPath failed");
        }

        $filesystem->rename($oldPhysicalPath, $newPhysicalPath);

        $file
            ->setFolderId($targetFolder->getId())
            ->setModifiedAt(new \DateTime)
            ->setModifyUserId($userId);
        try {
            $this->db->update(
                $this->fileTable,
                array(
                    'folder_id'      => $file->getFolderId(),
                    'modify_user_id' => $file->getModifyUserId(),
                    'modify_time'    => $file->getModifiedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'id = ?' => $file->getId()
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Move file failed.", 0, $e);
        }
    }

    public function deleteFile(FileInterface $file, $userId)
    {
        $filesystem = new Filesystem();

        $folder = $this->findFolderByFileId($file->getId());
        $physicalPath = $this->site->getRootDir() . $folder->getPath() . '/' . $file->getName();

        if ($filesystem->exists($physicalPath)) {
            if (!is_file($physicalPath)) {
                throw new IOException('Delete file failed, not a file.');
            }

            if (!is_writable(dirname($physicalPath))) {
                throw new NotWritableException("Delete file failed.");
            }
        }

        $this->db->delete(
            $this->fileTable,
            array(
                'id = ?' => $file->getId()
            )
        );

        if ($filesystem->exists($physicalPath)) {
            $filesystem->remove($physicalPath);
        }
    }

    private function deletePhysicalFolder(FolderInterface $folder, $userId)
    {
        $filesystem = new Filesystem();

        $physicalPath = $this->site->getRootDir() . $folder->getPath();

        if ($filesystem->exists($physicalPath) && !is_dir($physicalPath)) {
            throw new IOException('Delete folder failed, not a folder.');
        }

        if ($filesystem->exists($physicalPath) && !is_writable($physicalPath)) {
            throw new NotWritableException('Delete folder failed.');
        }

        foreach ($this->findFoldersByParentFolder($folder) as $subFolder) {
            $this->deletePhysicalFolder($subFolder, $userId);
        }

        foreach ($this->findFilesByFolder($folder) as $file) {
            $this->deleteFile($file, $userId);
        }

        $filesystem->remove($physicalPath);

        $folder->setId(null);
    }

    /**
     * {@inheritdoc}
     */
    private function mapFolderRows(array $rows)
    {
        $folders = array();
        foreach ($rows as $row) {
            $folders[] = $this->mapFolderRow($row);
        }

        return $folders;
    }

    /**
     * @param array $row
     *
     * @return Folder
     */
    private function mapFolderRow(array $row)
    {
        $physicalPath = $this->site->getRootDir() . '/' . $row['path'];
        $attributes = !empty($row['attributes']) ? json_decode($row['attributes'], true) : array();

        $folder = new Folder();
        $folder
            ->setSite($this->site)
            ->setId($row['id'])
            ->setParentId($row['parent_id'])
            ->setName($row['name'])
            ->setPath($row['path'])
            ->setPhysicalPath($physicalPath)
            ->setAttributes($attributes)
            ->setCreatedAt(new \DateTime($row['create_time']))
            ->setCreateUserId($row['create_user_id'])
            ->setModifiedAt(new \DateTime($row['modify_time']))
            ->setModifyUserId($row['modify_user_id']);

        return $folder;
    }

    /**
     * @param array $rows
     *
     * @return File[]
     */
    private function mapFileRows(array $rows)
    {
        $files = array();
        foreach ($rows as $row) {
            $files[] = $this->mapFileRow($row);
        }

        return $files;
    }

    /**
     * @param array $row
     *
     * @return File
     */
    private function mapFileRow(array $row)
    {
        $rootDir = rtrim($this->site->getRootDir(), '/');
        $path = trim($row['path'], '/');
        $name = trim($row['name'], '/');
        $physicalPath = $rootDir . '/' . ($path ? $path . '/' : '') . $name;

        $attributes = !empty($row['attributes']) ? json_decode($row['attributes'], true) : array();

        $file = new File();
        $file
            ->setSite($this->site)
            ->setId($row['id'])
            ->setVersion($row['version'])
            ->setFolderId($row['folder_id'])
            ->setName($row['name'])
            ->setMimeType($row['mime_type'])
            ->setHidden($row['hidden'])
            ->setPhysicalPath($physicalPath)
            ->setSize($row['size'])
            ->setHash($row['hash'])
            ->setAttributes($attributes)
            ->setCreatedAt(new \DateTime($row['create_time']))
            ->setCreateUserId($row['create_user_id'])
            ->setModifiedAt(new \DateTime($row['modify_time']))
            ->setModifyUserId($row['modify_user_id']);

        return $file;
    }
}
