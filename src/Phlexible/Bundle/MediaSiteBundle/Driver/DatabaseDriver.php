<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\ActionInterface;
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
use Phlexible\Bundle\MediaSiteBundle\Exception;
use Phlexible\Bundle\MediaSiteBundle\Exception\AlreadyExistsException;
use Phlexible\Bundle\MediaSiteBundle\Exception\IOException;
use Phlexible\Bundle\MediaSiteBundle\Exception\NotFoundException;
use Phlexible\Bundle\MediaSiteBundle\Exception\NotWritableException;
use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\FileSource\StreamSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\File;
use Phlexible\Bundle\MediaSiteBundle\Model\Folder;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Zend_Db_Adapter_Abstract as Connection;

/**
 * Database driver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DatabaseDriver extends AbstractDriver
{
    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var string
     */
    protected $folderTable;

    /**
     * @var string
     */
    protected $fileTable;

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
            ->where('site_id = ?', $this->getSite()->getId())
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
            ->where('site_id = ?', $this->getSite()->getId())
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
    public function findFileVersions($id)
    {
        $select = $this->db
            ->select()
            ->from($this->fileTable)
            ->where('id = ?', $id)
            ->order('version DESC');

        $rows = $this->db->fetchAll($select);

        return $this->mapFileRows($rows);
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
            ->where('fi.folder_id = ?', $folder->getId());

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

    /**
     * {@inheritdoc}
     */
    public function findLatestFiles($limit = 20)
    {
        $select = $this->db
            ->select()
            ->from(array('fi' => $this->fileTable))
            ->join(array('fo' => $this->folderTable), 'fo.id = fi.folder_id', array('path'))
            ->order('created_at DESC')
            ->limit($limit);

        $rows = $this->db->fetchAll($select);

        return $this->mapFileRows($rows);
    }

    /**
     * {@inheritdoc}
     */
    public function validateCreateFolder(CreateFolderAction $action)
    {
        $folderPath = trim($action->getFolder()->getPath() . '/' . $action->getFolder()->getName(), '/');

        $siteId = $this->getSite()->getId();

        $select = $this->db
            ->select()
            ->from($this->folderTable, 'id')
            ->where('path = ?', $folderPath)
            ->where('site_id = ?', $siteId);

        if ($this->db->fetchOne($select)) {
            throw new AlreadyExistsException("Create folder {$action->getFolder()->getName()} failed.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateRenameFolder(RenameFolderAction $action)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateCopyFolder(CopyFolderAction $action)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateMoveFolder(MoveFolderAction $action)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateDeleteFolder(DeleteFolderAction $action)
    {
        return;

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

    /**
     * {@inheritdoc}
     */
    public function validateCreateFile(CreateFileAction $action)
    {
        $select = $this->db
            ->select()
            ->from($this->fileTable, 'id')
            ->where('name = ?', $action->getFile()->getName())
            ->where('folder_id = ?', $action->getFile()->getFolderId());

        if ($this->db->fetchOne($select)) {
            throw new AlreadyExistsException("Create file {$action->getFile()->getName(
            )} failed, already exists in database.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateRenameFile(RenameFileAction $action)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateMoveFile(MoveFileAction $action)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateCopyFile(CopyFileAction $action)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateDeleteFile(DeleteFileAction $action)
    {
        return;

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

    /**
     * {@inheritdoc}
     */
    public function execute(ActionInterface $action)
    {
        $rc = new \ReflectionClass($action);
        $class = $rc->getShortName();

        $validate = 'validate' . $class;
        if (method_exists($this, $validate)) {
            $this->$validate($action);
        }

        $execute = 'execute' . $class;
        if (!method_exists($this, $execute)) {
            throw new \InvalidArgumentException("Invalid action $execute");
        }

        return $this->$execute($action);
    }

    private function executeCreateFileAction(CreateFileAction $action)
    {
        $filesystem = new Filesystem();
        $file = $action->getFile();

        $fileSource = $action->getFileSource();
        $path = $file->getPhysicalPath();

        if (!file_exists($path)) {
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
        }

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
                    'attributes'     => $file->getAttributes() ? json_encode($file->getAttributes()) : null,
                    'create_user_id' => $file->getCreateUserId(),
                    'created_at'     => $file->getCreatedAt()->format('Y-m-d H:i:s'),
                    'modify_user_id' => $file->getModifyUserId(),
                    'modified_at'    => $file->getModifiedAt()->format('Y-m-d H:i:s'),
                )
            );
        } catch (\Exception $e) {
            $filesystem->remove($path);

            throw new IOException("Create file {$file->getName()} failed.", 0, $e);
        }

        return $file;
    }

    private function executeRenameFileAction(RenameFileAction $action)
    {
        $file = $action->getFile();

        if ($file->getName() === $action->getName()) {
            return $action->getFile();
        }

        $oldName = $file->getName();
        $file
            ->setName($action->getName())
            ->setModifiedAt($action->getDate())
            ->setModifyUserId($action->getUserId());

        try {
            $this->db->update(
                $this->fileTable,
                array(
                    'name'           => $file->getName(),
                    'modify_user_id' => $file->getModifyUserId(),
                    'modified_at'    => $file->getModifiedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'id = ?' => $file->getId()
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Rename file from $oldName to {$file->getName()} failed.", 0, $e);
        }

        return $file;
    }

    private function executeMoveFileAction(MoveFileAction $action)
    {
        $file = $action->getFile();

        if ($file->getFolderId() === $action->getTargetFolder()->getId()) {
            return $file;
        }

        $file
            ->setFolderId($action->getTargetFolder()->getId())
            ->setModifiedAt($action->getDate())
            ->setModifyUserId($action->getUserId());

        try {
            $this->db->update(
                $this->fileTable,
                array(
                    'folder_id'      => $file->getFolderId(),
                    'modify_user_id' => $file->getModifyUserId(),
                    'modified_at'    => $file->getModifiedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'id = ?' => $file->getId()
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Move file failed.", 0, $e);
        }

        return $file;
    }

    private function executeCopyFileAction(CopyFileAction $action)
    {
        $file = $action->getFile();

        return $file;
    }

    private function executeDeleteFileAction(DeleteFileAction $action)
    {
        $file = $action->getFile();

        return $file;
    }

    private function executeCreateFolderAction(CreateFolderAction $action)
    {
        $folder = $action->getFolder();

        try {
            $this->db->insert(
                $this->folderTable,
                array(
                    'id'             => $folder->getId(),
                    'parent_id'      => $folder->getParentId(),
                    'site_id'        => $folder->getSite()->getId(),
                    'name'           => $folder->getName(),
                    'path'           => $folder->getPath(),
                    'create_user_id' => $folder->getCreateUserId(),
                    'created_at'     => $folder->getCreatedAt()->format('Y-m-d H:i:s'),
                    'modify_user_id' => $folder->getModifyUserId(),
                    'modified_at'    => $folder->getModifiedAt()->format('Y-m-d H:i:s'),
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Create folder {$folder->getName()} failed", 0, $e);
        }

        return $folder;
    }

    private function executeRenameFolderAction(RenameFolderAction $action)
    {
        $folder = $action->getFolder();

        $folder
            ->setName($action->getName())
            ->setModifiedAt($action->getDate())
            ->setModifyUserId($action->getUserId());

        if (!$folder->isRoot()) {
            $parentFolder = $this->findFolder($folder->getParentId());
            $newPath = ltrim($parentFolder->getPath() . '/' . $action->getName(), '/');

            $folder->setPath($newPath);
        }

        try {
            $this->db->update(
                $this->folderTable,
                array(
                    'name'           => $folder->getName(),
                    'modify_user_id' => $folder->getModifyUserId(),
                    'modified_at'    => $folder->getModifiedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'id = ?' => $folder->getId()
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Rename folder to {$folder->getName()} failed", 0, $e);
        }

        $parentFolder = $this->findFolder($folder->getParentId());
        $oldPath = $folder->getPath();
        $newPath = ltrim($parentFolder->getPath() . '/' . $action->getName(), '/');
        $oldPhysicalPath = $this->getSite()->getRootDir() . '/' . $oldPath;
        $newPhysicalPath = $this->getSite()->getRootDir() . '/' . $newPath;

        if (!empty($newPath) && !empty($oldPath)) {
            try {
                $this->db->update(
                    $this->folderTable,
                    array(
                        'path' => $this->db->fn->expr(
                                'REPLACE(path, ' . $this->db->quote($oldPath) . ', ' . $this->db->quote($newPath) . ')'
                            )
                    ),
                    array(
                        'site_id = ?' => $this->getSite()->getId(),
                        'path LIKE ?' => $oldPath . '%',
                    )
                );
            } catch (\Exception $e) {
                throw new IOException("Can't rename folder from $oldPath to $newPath failed", 0, $e);
            }
        }

        return $folder;
    }

    private function executeMoveFolderAction(MoveFolderAction $action)
    {
        $folder = $action->getFolder();

        if ($folder->getParentId() === $action->getTargetFolder()->getId()) {
            return null;
        }

        if ($folder->getId() === $action->getTargetFolder()->getId()) {
            return null;
        }

        $newPath = trim($action->getTargetFolder()->getPath() . '/' . $folder->getName(), '/');
        $oldPath = $folder->getPath();

        $folder
            ->setParentId($action->getTargetFolder()->getId())
            ->setPath($newPath)
            ->setModifiedAt($action->getDate())
            ->setModifyUserId($action->getUserId());

        try {
            $this->db->update(
                $this->folderTable,
                array(
                    'path'           => $folder->getPath(),
                    'parent_id'      => $action->getTargetFolder()->getId(),
                    'modify_user_id' => $folder->getModifyUserId(),
                    'modified_at'    => $folder->getModifiedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'id = ?' => $folder->getId()
                )
            );

            if ($action->getTargetFolder()->isRoot()) {
                $pathExpression = $this->db->fn->expr(
                    'CONCAT(' . $this->db->quote($action->getTargetFolder()->getPath()) . ', path)'
                );
            } else {
                $pathExpression = $this->db->fn->expr(
                    'REPLACE(path, ' . $this->db->quote(
                        $action->getTargetFolder()->getPath()
                    ) . ', ' . $this->db->quote($action->getTargetFolder()->getPath()) . ')'
                );
            }

            $this->db->update(
                $this->folderTable,
                array(
                    'path' => $pathExpression
                ),
                array(
                    'site_id = ?' => $this->getSite()->getId(),
                    'path LIKE ?' => $oldPath . '%',
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Move folder from $oldPath to $newPath failed.", 0, $e);
        }

        return $folder;
    }

    private function executeCopyFolderAction(CopyFolderAction $action)
    {
        $folder = $action->getFolder();

        return $folder;
    }

    private function executeDeleteFolderAction(DeleteFolderAction $action)
    {
        $folder = $action->getFolder();

        return $folder;
    }

    private function executeSetFileAttributesAction(SetFileAttributesAction $action)
    {
        try {
            $this->db->update(
                $this->fileTable,
                array(
                    'attributes' => $action->getAttributes() ? json_encode($action->getAttributes()) : null,
                ),
                array(
                    'id = ?' => $action->getFile()->getId(),
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Set file attributes failed.", 0, $e);
        }

        return $action->getFile();
    }

    private function executeSetFolderAttributesAction(SetFolderAttributesAction $action)
    {
        try {
            $this->db->update(
                $this->folderTable,
                array(
                    'attributes' => !$action->getAttributes() ? json_encode($action->getAttributes()) : null,
                ),
                array(
                    'id = ?' => $action->getFolder()->getId(),
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Set folder attributes failed.", 0, $e);
        }

        return $action->getFolder();
    }

    private function deletePhysicalFolder(FolderInterface $folder, $userId)
    {
        $filesystem = new Filesystem();

        $physicalPath = $this->getSite()->getRootDir() . $folder->getPath();

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
     * @param array $rows
     *
     * @return Folder[]
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
        $physicalPath = $this->getSite()->getRootDir() . '/' . $row['path'];
        $attributes = !empty($row['attributes']) ? json_decode($row['attributes'], true) : array();

        $folder = new Folder();
        $folder
            ->setSite($this->getSite())
            ->setId($row['id'])
            ->setParentId($row['parent_id'])
            ->setName($row['name'])
            ->setPath($row['path'])
            ->setPhysicalPath($physicalPath)
            ->setAttributes($attributes)
            ->setCreatedAt(new \DateTime($row['created_at']))
            ->setCreateUserId($row['create_user_id'])
            ->setModifiedAt(new \DateTime($row['modified_at']))
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
        $rootDir = rtrim($this->getSite()->getRootDir(), '/');
        $physicalPath = $rootDir . '/' . $row['hash'];

        $attributes = !empty($row['attributes']) ? json_decode($row['attributes'], true) : array();

        $file = new File();
        $file
            ->setSite($this->getSite())
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
            ->setCreatedAt(new \DateTime($row['created_at']))
            ->setCreateUserId($row['create_user_id'])
            ->setModifiedAt(new \DateTime($row['modified_at']))
            ->setModifyUserId($row['modify_user_id']);

        return $file;
    }
}
