<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
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
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
use Phlexible\Bundle\MediaSiteBundle\Exception\AlreadyExistsException;
use Phlexible\Bundle\MediaSiteBundle\Exception\IOException;
use Phlexible\Bundle\MediaSiteBundle\Exception\NotWritableException;
use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\FileSource\StreamSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Database driver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DoctrineDriver extends AbstractDriver
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $folderTable;

    /**
     * @var string
     */
    private $fileTable;

    /**
     * @var string
     */
    private $folderClass = 'Phlexible\Bundle\MediaManagerBundle\Entity\Folder';

    /**
     * @var string
     */
    private $fileClass = 'Phlexible\Bundle\MediaManagerBundle\Entity\File';

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->connection = $entityManager->getConnection();

        $this->folderTable = 'media_site_folder';
        $this->fileTable = 'media_site_file';
    }

    /**
     * @return EntityRepository
     */
    private function getFileRepository()
    {
        return $this->entityManager->getRepository($this->fileClass);
    }

    /**
     * @return EntityRepository
     */
    private function getFolderRepository()
    {
        return $this->entityManager->getRepository($this->folderClass);
    }

    /**
     * {@inheritdoc}
     */
    public function findFolder($id)
    {
        if ($id === -1) {
            return $this->findRootFolder();
        }

        $folder = $this->getFolderRepository()->findOneBy(
            array(
                'siteId' => $this->getSite()->getId(),
                'id'     => $id
            )
        );

        if ($folder) {
            $folder->setSite($this->getSite());
        }

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function findRootFolder()
    {
        $folder = $this->getFolderRepository()->findOneBy(
            array(
                'siteId'   => $this->getSite()->getId(),
                'parentId' => null
            )
        );

        if ($folder) {
            $folder->setSite($this->getSite());
        }

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function findFolderByPath($path)
    {
        $path = ltrim($path, '/');

        $folder = $this->getFolderRepository()->findOneBy(
            array(
                'siteId' => $this->getSite()->getId(),
                'path'   => $path
            )
        );

        if ($folder) {
            $folder->setSite($this->getSite());
        }

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function findFoldersByParentFolder(FolderInterface $parentFolder)
    {
        $folders = $this->getFolderRepository()->findBy(
            array(
                'parentId' => $parentFolder->getId(),
            )
        );

        foreach ($folders as $folder) {
            $folder->setSite($this->getSite());
        }

        return $folders;
    }

    /**
     * {@inheritdoc}
     */
    public function countFoldersByParentFolder(FolderInterface $parentFolder)
    {
        $qb = $this->getFolderRepository()->createQueryBuilder('fo');
        $qb
            ->select('COUNT(fo.id)')
            ->where($qb->expr()->eq('fo.parentId', $qb->expr()->literal($parentFolder->getId())));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findFolderByFileId($fileId)
    {
        $file = $this->findFile($fileId);

        if (!$file) {
            return null;
        }

        $folder = $file->getFolder();
        $folder->setSite($this->getSite());

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function findFile($id, $version = 1)
    {
        $file = $this->getFileRepository()->findOneBy(
            array(
                'id'      => $id,
                'version' => $version
            )
        );

        if ($file) {
            $file->setSite($this->getSite());
        }

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function findFileByPath($path, $version = 1)
    {
        $name = basename($path);
        $folderPath = trim(dirname($path), '/');

        $folder = $this->findFolderByPath($folderPath);

        $file = $this->getFileRepository()->findOneBy(
            array(
                'name'   => $name,
                'folder' => $folder
            )
        );

        if ($file) {
            $file->setSite($this->getSite());
        }

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function findFileVersions($id)
    {
        $files = $this->getFileRepository()->findBy(
            array(
                'id' => $id
            )
        );

        foreach ($files as $file) {
            $file->setSite($this->getSite());
        }

        return $files;
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
        $criteria = array(
            'folder' => $folder
        );

        if (!$includeHidden) {
            $criteria['hidden'] = false;
        }

        $files = $this->getFileRepository()->findBy(
            $criteria,
            $order,
            $limit,
            $start
        );

        foreach ($files as $file) {
            $file->setSite($this->getSite());
        }

        return $files;
    }

    /**
     * {@inheritdoc}
     */
    public function countFilesByFolder(FolderInterface $folder)
    {
        $qb = $this->getFileRepository()->createQueryBuilder('fi');
        $qb
            ->select('COUNT(fi.id)')
            ->where($qb->expr()->eq('fi.folder', $qb->expr()->literal($folder->getId())));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findLatestFiles($limit = 20)
    {
        $files = $this->getFileRepository()->findBy(array(), array('createdAt' => 'DESC'), $limit);

        foreach ($files as $file) {
            $file->setSite($this->getSite());
        }

        return $files;
    }

    /**
     * {@inheritdoc}
     */
    public function search($query)
    {
        $qb = $this->getFileRepository()->createQueryBuilder('fi');
        $qb->where($qb->expr()->like('fi.name', $qb->expr()->literal("%$query%")));

        $files = $qb->getQuery()->getResult();

        foreach ($files as $file) {
            $file->setSite($this->getSite());
        }

        return $files;
    }

    /**
     * {@inheritdoc}
     */
    public function validateCreateFolderAction(CreateFolderAction $action)
    {
        $folderPath = trim($action->getTargetFolder()->getPath() . '/' . $action->getName(), '/');

        if ($this->findFolderByPath($folderPath)) {
            throw new AlreadyExistsException("Create folder {$action->getName()} failed.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateRenameFolderAction(RenameFolderAction $action)
    {
        $parentFolder = $this->findFolder($action->getFolder()->getParentId());
        $folderPath = trim($parentFolder->getPath() . '/' . $action->getName(), '/');

        if ($this->findFolderByPath($folderPath)) {
            throw new AlreadyExistsException("Rename folder to {$action->getName()} failed.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateCopyFolderAction(CopyFolderAction $action)
    {
        $folderPath = trim($action->getTargetFolder()->getPath() . '/' . $action->getFolder()->getName(), '/');

        if ($this->findFolderByPath($folderPath)) {
            throw new AlreadyExistsException("Copy folder {$action->getFolder()->getName()} failed.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateMoveFolderAction(MoveFolderAction $action)
    {
        $folderPath = trim($action->getTargetFolder()->getPath() . '/' . $action->getFolder()->getName(), '/');

        if ($this->findFolderByPath($folderPath)) {
            throw new AlreadyExistsException("Move folder {$action->getFolder()->getName()} failed.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateDeleteFolderAction(DeleteFolderAction $action)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateCreateFileAction(CreateFileAction $action)
    {
        if ($this->findFileByPath($action->getTargetFolder()->getPath() . '/' . $action->getFileSource()->getName())) {
            throw new AlreadyExistsException("Create file {$action->getFileSource()->getName()} failed, already exists in database.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateRenameFileAction(RenameFileAction $action)
    {
        $folder = $this->findFolder($action->getFile()->getFolderId());
        if ($this->findFileByPath($folder->getPath() . '/' . $action->getName())) {
            throw new AlreadyExistsException("Rename file to {$action->getName()} failed, already exists in database.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateMoveFileAction(MoveFileAction $action)
    {
        if ($this->findFileByPath($action->getTargetFolder()->getPath() . '/' . $action->getFile()->getName())) {
            throw new AlreadyExistsException("Move file {$action->getFile()->getName()} failed, already exists in database.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateCopyFileAction(CopyFileAction $action)
    {
        $folder = $this->findFolder($action->getFile()->getFolderId());
        if ($this->findFileByPath($folder->getPath() . '/' . $action->getFile()->getName())) {
            throw new AlreadyExistsException("Copy file {$action->getFile()->getName()} failed, already exists in database.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateDeleteFileAction(DeleteFileAction $action)
    {
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
     * @return FileInterface
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
        $fileClass = $this->fileClass;
        $file = new $fileClass();
        $file
            ->setSite($this->getSite())
            ->setId(Uuid::generate())
            ->setFolder($targetFolder)
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
            $this->entityManager->persist($file);
            $this->entityManager->flush($file);
        } catch (\Exception $e) {
            $filesystem->remove($path);

            throw new IOException("Create file {$file->getName()} failed.", 0, $e);
        }

        return $file;
    }

    /**
     * @param ReplaceFileAction $action
     *
     * @return FileInterface
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
            $this->entityManager->flush($file);
        } catch (\Exception $e) {
            $filesystem->remove($path);

            throw new IOException("Create file {$file->getName()} failed.", 0, $e);
        }

        return $file;
    }

    /**
     * @param RenameFileAction $action
     *
     * @return FileInterface
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
            $this->entityManager->flush($file);
        } catch (\Exception $e) {
            throw new IOException("Rename file from $oldName to {$file->getName()} failed.", 0, $e);
        }

        return $file;
    }

    /**
     * @param MoveFileAction $action
     *
     * @return FileInterface
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
            $this->entityManager->flush($file);
        } catch (\Exception $e) {
            throw new IOException("Move file failed.", 0, $e);
        }

        return $file;
    }

    /**
     * @param CopyFileAction $action
     *
     * @return FileInterface
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
            $this->entityManager->flush($file);
        } catch (\Exception $e) {
            throw new IOException("Copy file failed.", 0, $e);
        }

        return $file;
    }

    /**
     * @param DeleteFileAction $action
     *
     * @return FileInterface
     * @throws IOException
     */
    private function executeDeleteFileAction(DeleteFileAction $action)
    {
        $file = $action->getFile();

        try {
            $this->entityManager->remove($file);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new IOException("Copy file failed.", 0, $e);
        }

        return $file;
    }

    /**
     * @param SetFileAttributesAction $action
     *
     * @return FileInterface
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
            $this->entityManager->flush($file);
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
            ->setAttributes($action->getAttributes())
            ->setCreatedAt($action->getDate())
            ->setCreateUserid($action->getUserId())
            ->setModifiedAt($folder->getCreatedAt())
            ->setModifyUserid($folder->getCreateUserId());

        try {
            $this->entityManager->persist($folder);
            $this->entityManager->flush($folder);
        } catch (\Exception $e) {
            throw new IOException("Create folder {$folder->getName()} failed", 0, $e);
        }

        return $folder;
    }

    /**
     * @param RenameFolderAction $action
     *
     * @return FolderInterface
     * @throws IOException
     */
    private function executeRenameFolderAction(RenameFolderAction $action)
    {
        $folder = $action->getFolder();

        $folder
            ->setName($action->getName())
            ->setModifiedAt($action->getDate())
            ->setModifyUserId($action->getUserId());

        try {
            $this->entityManager->flush($folder);
        } catch (\Exception $e) {
            throw new IOException("Rename folder to {$folder->getName()} failed", 0, $e);
        }

        if (!$folder->isRoot()) {
            $oldPath = $folder->getPath();
            $parentFolder = $this->findFolder($folder->getParentId());
            $newPath = ltrim($parentFolder->getPath() . '/' . $action->getName(), '/');
            $folder->setPath($newPath);

            try {
                $qb = $this->getFolderRepository()->createQueryBuilder('fo');
                $qb
                    ->update($this->folderTable, 'f')
                    ->set('f.path', 'REPLACE(path, ' . $qb->expr()->literal($oldPath) . ', ' . $qb->expr()->literal($newPath) . ')')
                    ->where($qb->expr()->eq('f.site_id', $qb->expr()->literal($this->getSite()->getId())))
                    ->andWhere($qb->expr()->orX(
                        $qb->expr()->eq('f.path', $qb->expr()->literal($oldPath)),
                        $qb->expr()->like('f.path', $qb->expr()->literal("$oldPath/%"))
                    ));

                $qb->getQuery()->execute();
            } catch (\Exception $e) {
                throw new IOException("Rename folder from $oldPath to $newPath failed.", 0, $e);
            }
        }

        return $folder;
    }

    /**
     * @param MoveFolderAction $action
     *
     * @return FolderInterface
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
            $this->entityManager->flush($folder);

            $qb = $this->getFolderRepository()->createQueryBuilder('fo');

            if ($action->getTargetFolder()->isRoot()) {
                $pathExpression = 'CONCAT(' . $qb->expr()->literal($action->getTargetFolder()->getPath()) . ', path)';
            } else {
                $pathExpression = 'REPLACE(path, ' . $qb->expr()->literal($action->getTargetFolder()->getPath()) . ', ' . $qb->expr()->literal($action->getTargetFolder()->getPath()) . ')';
            }

            $qb
                ->update($this->folderTable, 'f')
                ->set('f.path', $pathExpression)
                ->where($qb->expr()->eq('f.site_id', $qb->expr()->literal($this->getSite()->getId())))
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->eq('f.path', $qb->expr()->literal($oldPath)),
                        $qb->expr()->like('f.path', $qb->expr()->literal("$oldPath/%"))
                    )
                );

            $qb->getQuery()->execute();
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
     * @return FolderInterface
     * @throws IOException
     */
    private function executeDeleteFolderAction(DeleteFolderAction $action)
    {
        $folder = $action->getFolder();

        try {
            $this->entityManager->remove($folder);
        } catch (\Exception $e) {
            throw new IOException("Delete folder failed.", 0, $e);
        }

        return $folder;
    }

    /**
     * @param SetFolderAttributesAction $action
     *
     * @return FolderInterface
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
            $this->entityManager->flush($folder);
        } catch (\Exception $e) {
            throw new IOException("Set folder attributes failed.", 0, $e);
        }

        return $action->getFolder();
    }

    /**
     * @param FolderInterface $folder
     * @param string          $userId
     *
     * @throws NotWritableException
     * @throws IOException
     */
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
}
