<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
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
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\ReplaceFileAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\SetFileAttributesAction;
use Phlexible\Bundle\MediaSiteBundle\Driver\Action\SetFolderAttributesAction;
use Phlexible\Bundle\MediaSiteBundle\Exception\AlreadyExistsException;
use Phlexible\Bundle\MediaSiteBundle\Exception\IOException;
use Phlexible\Bundle\MediaSiteBundle\Exception\NotFoundException;
use Phlexible\Bundle\MediaSiteBundle\Exception\NotWritableException;
use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\FileSource\StreamSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;
use Phlexible\Bundle\MediaSiteBundle\Model\File;
use Phlexible\Bundle\MediaSiteBundle\Model\Folder;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Database driver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DoctrineDriver extends AbstractDriver
{
    /**
     * @var Connection
     */
    protected $connection;

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
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->folderTable = 'media_site_folder';
        $this->fileTable = 'media_site_file';
    }

    /**
     * {@inheritdoc}
     */
    public function findFolder($id)
    {
        if ($id === -1) {
            return $this->findRootFolder();
        }

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('fo.*')
            ->from($this->folderTable, 'fo')
            ->where($qb->expr()->eq('fo.id', $qb->expr()->literal($id)));

        $row = $this->connection->fetchAssoc($qb->getSQL());

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
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('fo.*')
            ->from($this->folderTable, 'fo')
            ->where($qb->expr()->eq('fo.site_id', $qb->expr()->literal($this->getSite()->getId())))
            ->andWhere($qb->expr()->isNull('fo.parent_id'));

        $row = $this->connection->fetchAssoc($qb->getSQL());

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

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('fo.*')
            ->from($this->folderTable, 'fo')
            ->where($qb->expr()->eq('fo.site_id', $qb->expr()->literal($this->getSite()->getId())))
            ->andWhere($qb->expr()->eq('fo.path', $qb->expr()->literal($path)));

        $row = $this->connection->fetchAssoc($qb->getSQL());

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
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('fo.*')
            ->from($this->folderTable, 'fo')
            ->where($qb->expr()->eq('fo.parent_id', $qb->expr()->literal($parentFolder->getId())));

        $rows = $this->connection->fetchAll($qb->getSQL());

        return $this->mapFolderRows($rows);
    }

    /**
     * {@inheritdoc}
     */
    public function countFoldersByParentFolder(FolderInterface $parentFolder)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('COUNT(fo.id)')
            ->from($this->folderTable, 'fo')
            ->where($qb->expr()->eq('fo.parent_id', $qb->expr()->literal($parentFolder->getId())));

        return $this->connection->fetchColumn($qb->getSQL());
    }

    /**
     * {@inheritdoc}
     */
    public function findFolderByFileId($fileId)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('fo.*')
            ->from($this->folderTable, 'fo')
            ->join('fo', $this->fileTable, 'fi', 'fo.id = fi.folder_id')
            ->where($qb->expr()->eq('fi.id', $qb->expr()->literal($fileId)));

        $row = $this->connection->fetchAssoc($qb->getSQL());

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
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('fi.*')
            ->from($this->fileTable, 'fi')
            ->where($qb->expr()->eq('fi.id', $qb->expr()->literal($id)))
            ->andWhere($qb->expr()->eq('fi.version', $version));

        $row = $this->connection->fetchAssoc($qb->getSQL());

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
        $folderPath = trim(dirname($path), '/');

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('fi.*')
            ->from($this->fileTable, 'fi')
            ->join('fi', $this->folderTable, 'fo', 'fo.id = fi.folder_id')
            ->where($qb->expr()->eq('fo.path', $qb->expr()->literal($folderPath)))
            ->andWhere($qb->expr()->eq('fi.name', $qb->expr()->literal($name)))
            ->andWhere($qb->expr()->eq('fi.version', $version));

        $row = $this->connection->fetchAssoc($qb->getSQL());

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
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('fi.*')
            ->from($this->fileTable, 'fi')
            ->where($qb->expr()->eq('fi.id', $qb->expr()->literal($id)))
            ->orderBy('fi.version', 'DESC');

        $rows = $this->connection->fetchAll($qb->getSQL());

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
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('fi.*')
            ->from($this->fileTable, 'fi')
            ->join('fi', $this->folderTable, 'fo', 'fo.id = fi.folder_id')
            ->where($qb->expr()->eq('fi.folder_id', $qb->expr()->literal($folder->getId())));

        if ($order) {
            foreach ($order as $field => $dir) {
                $qb->orderBy($field, $dir);
            }
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }
        if ($start) {
            $qb->setFirstResult($start);
        }

        if (!$includeHidden) {
            $qb->andWhere('fi.hidden = 0');
        }

        $rows = $this->connection->fetchAll($qb->getSQL());

        return $this->mapFileRows($rows);
    }

    /**
     * {@inheritdoc}
     */
    public function countFilesByFolder(FolderInterface $folder)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('COUNT(fi.id)')
            ->from($this->fileTable, 'fi')
            ->where($qb->expr()->eq('fi.folder_id', $qb->expr()->literal($folder->getId())));

        return $this->connection->fetchColumn($qb->getSQL());
    }

    /**
     * {@inheritdoc}
     */
    public function findLatestFiles($limit = 20)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('fi.*')
            ->from($this->fileTable, 'fi')
            ->join('fi', $this->folderTable, 'fo', 'fo.id = fi.folder_id')
            ->orderBy('fi.created_at DESC');

        $rows = $this->connection->fetchAll($qb->getSQL());

        return $this->mapFileRows($rows);
    }

    /**
     * {@inheritdoc}
     */
    public function validateCreateFolder(CreateFolderAction $action)
    {
        $folderPath = trim($action->getFolder()->getPath() . '/' . $action->getFolder()->getName(), '/');

        $siteId = $this->getSite()->getId();

        if ($this->findFolderByPath($folderPath)) {
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
            $this->connection->delete(
                $this->folderTable,
                array(
                    'site_id' => $this->site->getId(),
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
        if ($this->findFileByPath($action->getTargetFolder()->getPath . '/' . $action->getFile()->getName())) {
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

        $this->connection->delete(
            $this->fileTable,
            array(
                'id' => $file->getId()
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

    /**
     * @param CreateFileAction $action
     *
     * @return File
     * @throws IOException
     */
    private function executeCreateFileAction(CreateFileAction $action)
    {
        $hashCalculator = $action->getHashCalulator();
        $fileSource = $action->getFileSource();
        $targetFolder = $action->getTargetFolder();
        $userId = $action->getUserId();
        $attributes = $action->getAttributes();

        $hash = $hashCalculator->fromFileSource($fileSource);
        $path = $this->getSite()->getRootDir() . $hash;

        // prepare folder's name and id
        $file = new File();
        $file
            ->setSite($this->getSite())
            ->setId(Uuid::generate())
            ->setFolderId($targetFolder->getId())
            ->setPhysicalPath($path)
            ->setName($fileSource->getName())
            ->setCreatedAt($action->getDate())
            ->setCreateUserid($action->getUserId())
            ->setModifiedAt($file->getCreatedAt())
            ->setModifyUserid($file->getCreateUserId())
            ->setMimeType($fileSource->getMimeType())
            ->setSize($fileSource->getSize())
            ->setHash($hash)
            ->setAttributes($attributes);

        $fileSource = $action->getFileSource();
        $path = $file->getPhysicalPath();

        $filesystem = new Filesystem();

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
            $this->connection->insert(
                $this->fileTable,
                array(
                    'id'             => $file->getId(),
                    'folder_id'      => $file->getFolderId(),
                    'name'           => $file->getName(),
                    'mime_type'      => $file->getMimeType(),
                    'size'           => $file->getSize(),
                    'hash'           => $file->getHash(),
                    'attributes'     => count($file->getAttributes()) ? serialize($file->getAttributes()) : null,
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

    /**
     * @param ReplaceFileAction $action
     *
     * @return File
     * @throws IOException
     */
    private function executeReplaceFileAction(ReplaceFileAction $action)
    {
        $file = $action->getFile();
        $hashCalculator = $action->getHashCalulator();
        $fileSource = $action->getFileSource();
        $attributes = $action->getAttributes();

        $hash = $hashCalculator->fromFileSource($fileSource);
        $path = $this->getSite()->getRootDir() . $hash;

        $file
            ->setName($fileSource->getName())
            ->setPhysicalPath($path)
            ->setSize($fileSource->getSize())
            ->setHash($hash)
            ->setAttributes($attributes)
            ->setModifiedAt($action->getDate())
            ->setModifyUserid($action->getUserId());

        $filesystem = new Filesystem();

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
            $this->connection->update(
                $this->fileTable,
                array(
                    'name'           => $file->getName(),
                    'mime_type'      => $file->getMimeType(),
                    'size'           => $file->getSize(),
                    'hash'           => $file->getHash(),
                    'attributes'     => count($file->getAttributes()) ? serialize($file->getAttributes()) : null,
                    'modify_user_id' => $file->getModifyUserId(),
                    'modified_at'    => $file->getModifiedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'id' => $file->getId(),
                )
            );
        } catch (\Exception $e) {
            $filesystem->remove($path);

            throw new IOException("Create file {$file->getName()} failed.", 0, $e);
        }

        return $file;
    }

    /**
     * @param RenameFileAction $action
     *
     * @return File
     * @throws IOException
     */
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
            $this->connection->update(
                $this->fileTable,
                array(
                    'name'           => $file->getName(),
                    'modify_user_id' => $file->getModifyUserId(),
                    'modified_at'    => $file->getModifiedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'id' => $file->getId()
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Rename file from $oldName to {$file->getName()} failed.", 0, $e);
        }

        return $file;
    }

    /**
     * @param MoveFileAction $action
     *
     * @return File
     * @throws IOException
     */
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
            $this->connection->update(
                $this->fileTable,
                array(
                    'folder_id'      => $file->getFolderId(),
                    'modify_user_id' => $file->getModifyUserId(),
                    'modified_at'    => $file->getModifiedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'id' => $file->getId()
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Move file failed.", 0, $e);
        }

        return $file;
    }

    /**
     * @param CopyFileAction $action
     *
     * @return File
     * @throws IOException
     */
    private function executeCopyFileAction(CopyFileAction $action)
    {
        $originalFile = $action->getFile();
        $targetFolder = $action->getTargetFolder();

        $file = clone $originalFile;
        $file
            ->setId(Uuid::generate())
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($action->getUserId())
            ->setModifiedAt($file->getCreatedAt())
            ->setModifyUserId($file->getCreateUserId())
            ->setFolderId($targetFolder->getId());

        try {
            $this->connection->insert(
                $this->fileTable,
                array(
                    'id'             => $file->getId(),
                    'folder_id'      => $file->getFolderId(),
                    'name'           => $file->getName(),
                    'mime_type'      => $file->getMimeType(),
                    'size'           => $file->getSize(),
                    'hash'           => $file->getHash(),
                    'attributes'     => count($file->getAttributes()) ? serialize($file->getAttributes()) : null,
                    'create_user_id' => $file->getCreateUserId(),
                    'created_at'     => $file->getCreatedAt()->format('Y-m-d H:i:s'),
                    'modify_user_id' => $file->getModifyUserId(),
                    'modified_at'    => $file->getModifiedAt()->format('Y-m-d H:i:s'),
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Copy file failed.", 0, $e);
        }

        return $file;
    }

    /**
     * @param DeleteFileAction $action
     *
     * @return File
     * @throws IOException
     */
    private function executeDeleteFileAction(DeleteFileAction $action)
    {
        $file = $action->getFile();

        try {
            $this->connection->delete(
                $this->fileTable,
                array(
                    'id' => $file->getId(),
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Copy file failed.", 0, $e);
        }

        return $file;
    }

    /**
     * @param SetFileAttributesAction $action
     *
     * @return File
     * @throws IOException
     */
    private function executeSetFileAttributesAction(SetFileAttributesAction $action)
    {
        $file = $action->getFile();

        $file
            ->setModifiedAt($action->getDate())
            ->setModifyUserId($action->getUserId())
            ->setAttributes($action->getAttributes());

        try {
            $this->connection->update(
                $this->fileTable,
                array(
                    'attributes'     => count($file->getAttributes()) ? serialize($file->getAttributes()) : null,
                    'modify_user_id' => $file->getModifyUserId(),
                    'modified_at'    => $file->getModifiedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'id' => $file->getId(),
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Set file attributes failed.", 0, $e);
        }

        return $action->getFile();
    }

    /**
     * @param CreateFolderAction $action
     *
     * @return FolderInterface
     * @throws IOException
     */
    private function executeCreateFolderAction(CreateFolderAction $action)
    {
        $targetFolder = $action->getTargetFolder();
        $name = $action->getName();
        $userId = $action->getUserId();

        $folderPath = trim($targetFolder->getPath() . '/' . $name, '/');

        // prepare folder's name and id
        $folder = new Folder();
        $folder
            ->setSite($this->getSite())
            ->setId(Uuid::generate())
            ->setName($name)
            ->setParentId($targetFolder->getId())
            ->setPath($folderPath)
            ->setCreatedAt($action->getDate())
            ->setCreateUserid($action->getUserId())
            ->setModifiedAt($folder->getCreatedAt())
            ->setModifyUserid($folder->getCreateUserId());

        try {
            $this->connection->insert(
                $this->folderTable,
                array(
                    'id'             => $folder->getId(),
                    'parent_id'      => $folder->getParentId(),
                    'site_id'        => $folder->getSite()->getId(),
                    'name'           => $folder->getName(),
                    'path'           => $folder->getPath(),
                    'attributes'     => count($folder->getAttributes()) ? serialize($folder->getAttributes()) : null,
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

    /**
     * @param RenameFolderAction $action
     *
     * @return Folder
     * @throws IOException
     */
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
            $this->connection->update(
                $this->folderTable,
                array(
                    'name'           => $folder->getName(),
                    'modify_user_id' => $folder->getModifyUserId(),
                    'modified_at'    => $folder->getModifiedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'id' => $folder->getId()
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
                $this->connection->update(
                    $this->folderTable,
                    array(
                        'path' => $this->connection->fn->expr(
                            'REPLACE(path, ' . $this->connection->quote($oldPath) . ', ' . $this->connection->quote($newPath) . ')'
                        )
                    ),
                    array(
                        'site_id' => $this->getSite()->getId(),
                        'path LIKE ?' => $oldPath . '%',
                    )
                );
            } catch (\Exception $e) {
                throw new IOException("Can't rename folder from $oldPath to $newPath failed", 0, $e);
            }
        }

        return $folder;
    }

    /**
     * @param MoveFolderAction $action
     *
     * @return Folder
     * @throws IOException
     */
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
            $this->connection->update(
                $this->folderTable,
                array(
                    'path'           => $folder->getPath(),
                    'parent_id'      => $action->getTargetFolder()->getId(),
                    'modify_user_id' => $folder->getModifyUserId(),
                    'modified_at'    => $folder->getModifiedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'id' => $folder->getId()
                )
            );

            if ($action->getTargetFolder()->isRoot()) {
                $pathExpression = $this->connection->fn->expr(
                    'CONCAT(' . $this->connection->quote($action->getTargetFolder()->getPath()) . ', path)'
                );
            } else {
                $pathExpression = $this->connection->fn->expr(
                    'REPLACE(path, ' . $this->connection->quote(
                        $action->getTargetFolder()->getPath()
                    ) . ', ' . $this->connection->quote($action->getTargetFolder()->getPath()) . ')'
                );
            }

            $this->connection->update(
                $this->folderTable,
                array(
                    'path' => $pathExpression
                ),
                array(
                    'site_id' => $this->getSite()->getId(),
                    'path LIKE ?' => $oldPath . '%',
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Move folder from $oldPath to $newPath failed.", 0, $e);
        }

        return $folder;
    }

    /**
     * @param CopyFolderAction $action
     *
     * @return FolderInterface
     */
    private function executeCopyFolderAction(CopyFolderAction $action)
    {
        $folder = $action->getFolder();

        return $folder;
    }

    /**
     * @param DeleteFolderAction $action
     *
     * @return Folder
     * @throws IOException
     */
    private function executeDeleteFolderAction(DeleteFolderAction $action)
    {
        $folder = $action->getFolder();

        try {
            $this->connection->delete(
                $this->folderTable,
                array(
                    'id' => $folder->getId(),
                )
            );
        } catch (\Exception $e) {
            throw new IOException("Delete folder failed.", 0, $e);
        }

        return $folder;
    }

    /**
     * @param SetFolderAttributesAction $action
     *
     * @return Folder
     * @throws IOException
     */
    private function executeSetFolderAttributesAction(SetFolderAttributesAction $action)
    {
        $folder = $action->getFolder();

        $folder
            ->setModifiedAt($action->getDate())
            ->setModifyUserId($action->getUserId())
            ->setAttributes($action->getAttributes());

        try {
            $this->connection->update(
                $this->folderTable,
                array(
                    'attributes'     => count($folder->getAttributes()) ? serialize($folder->getAttributes()) : null,
                    'modify_user_id' => $folder->getModifyUserId(),
                    'modified_at'    => $folder->getModifiedAt()->format('Y-m-d H:i:s'),
                ),
                array(
                    'id' => $folder->getId(),
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

        $attributes = !empty($row['attributes']) ? unserialize($row['attributes']) : new AttributeBag();

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

        $attributes = !empty($row['attributes']) ? unserialize($row['attributes']) : new AttributeBag();

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
