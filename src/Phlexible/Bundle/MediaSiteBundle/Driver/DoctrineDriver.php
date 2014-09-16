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
use Phlexible\Bundle\MediaSiteBundle\Event\CopyFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\CreateFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\FileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\FolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\MoveFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\MoveFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\RenameFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\RenameFolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\ReplaceFileEvent;
use Phlexible\Bundle\MediaSiteBundle\FileSource\FileSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\FileSource\FilesystemFileSource;
use Phlexible\Bundle\MediaSiteBundle\HashCalculator\HashCalculatorInterface;
use Phlexible\Bundle\MediaSiteBundle\MediaSiteEvents;
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
use Phlexible\Bundle\MediaSiteBundle\Exception\AlreadyExistsException;
use Phlexible\Bundle\MediaSiteBundle\Exception\IOException;
use Phlexible\Bundle\MediaSiteBundle\Exception\NotWritableException;
use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\FileSource\StreamSourceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var HashCalculatorInterface
     */
    private $hashCalculator;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var string
     */
    private $folderTable = 'media_site_folder';

    /**
     * @var string
     */
    private $fileTable = 'media_site_file';

    /**
     * @var string
     */
    private $folderClass = 'Phlexible\Bundle\MediaManagerBundle\Entity\Folder';

    /**
     * @var string
     */
    private $fileClass = 'Phlexible\Bundle\MediaManagerBundle\Entity\File';

    /**
     * @param EntityManager            $entityManager
     * @param HashCalculatorInterface  $hashCalculator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManager $entityManager, HashCalculatorInterface $hashCalculator, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->hashCalculator = $hashCalculator;
        $this->eventDispatcher = $eventDispatcher;

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
            /* @var $file FileInterface */
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
            /* @var $file FileInterface */
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
            /* @var $file FileInterface */
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
            /* @var $file FileInterface */
            $file->setSite($this->getSite());
        }

        return $files;
    }
    /**
     * @param FolderInterface     $targetFolder
     * @param FileSourceInterface $fileSource
     * @param AttributeBag        $attributes
     * @param string              $userId
     *
     * @return FileInterface
     * @throws IOException
     */
    public function createFile(FolderInterface $targetFolder, FileSourceInterface $fileSource, AttributeBag $attributes, $userId)
    {
        $hash = $this->hashCalculator->fromFileSource($fileSource);

        // prepare folder's name and id
        $fileClass = $this->fileClass;
        $file = new $fileClass();
        /* @var $file FileInterface */
        $file
            ->setSite($this->getSite())
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

        $this->validateCreateFile($file);

        $event = new CreateFileEvent($file, $fileSource);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_CREATE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Create file {$file->getName()} failed.");
        }

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

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(MediaSiteEvents::CREATE_FILE, $event);

        return $file;
    }

    /**
     * @param FileInterface       $file
     * @param FileSourceInterface $fileSource
     * @param AttributeBag        $attributes
     * @param string              $userId
     *
     * @return FileInterface
     * @throws IOException
     */
    public function replaceFile(FileInterface $file, FileSourceInterface $fileSource, AttributeBag $attributes, $userId)
    {
        $hash = $this->hashCalculator->fromFileSource($fileSource);

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

        $filesystem = new Filesystem();
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

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(MediaSiteEvents::REPLACE_FILE, $event);

        return $file;
    }

    /**
     * @param FileInterface $file
     * @param string        $name
     * @param string        $userId
     *
     * @return FileInterface
     * @throws IOException
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

        $this->validateRenameFile($file);

        $event = new RenameFileEvent($file, $oldName);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_RENAME_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Rename file {$file->getName()} cancelled.");
        }

        try {
            $this->entityManager->flush($file);
        } catch (\Exception $e) {
            throw new IOException("Rename file from $oldName to {$file->getName()} failed.", 0, $e);
        }

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(MediaSiteEvents::RENAME_FILE, $event);

        return $file;
    }

    /**
     * @param FileInterface   $file
     * @param FolderInterface $targetFolder
     * @param string          $userId
     *
     * @return FileInterface
     * @throws IOException
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

        $this->validateMoveFile($file);

        $event = new MoveFileEvent($file, $targetFolder);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_MOVE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Move file {$file->getName()} cancelled.");
        }

        try {
            $this->entityManager->flush($file);
        } catch (\Exception $e) {
            throw new IOException("Move file failed.", 0, $e);
        }

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(MediaSiteEvents::MOVE_FILE, $event);

        return $file;
    }

    /**
     * @param FileInterface   $originalFile
     * @param FolderInterface $targetFolder
     * @param string          $userId
     *
     * @return FileInterface
     * @throws IOException
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

        $this->validateCopyFile($file);

        $event = new CopyFileEvent($file, $originalFile, $targetFolder);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_COPY_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Copy file {$file->getName()} cancelled.");
        }

        try {
            $this->entityManager->flush($file);
        } catch (\Exception $e) {
            throw new IOException("Copy file failed.", 0, $e);
        }

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(MediaSiteEvents::COPY_FILE, $event);

        return $file;
    }

    /**
     * @param FileInterface $file
     * @param string        $userId
     *
     * @return FileInterface
     * @throws IOException
     */
    public function deleteFile(FileInterface $file, $userId)
    {
        $event = new FileEvent($file);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_DELETE_FILE, $event)->isPropagationStopped()) {
            throw new IOException("Delete file {$file->getName()} cancelled.");
        }

        try {
            $this->entityManager->remove($file);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new IOException("Copy file failed.", 0, $e);
        }

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(MediaSiteEvents::DELETE_FILE, $event);

        return $file;
    }

    /**
     * @param FileInterface $file
     * @param AttributeBag  $attributes
     * @param string        $userId
     *
     * @return FileInterface
     * @throws IOException
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

        try {
            $this->entityManager->flush($file);
        } catch (\Exception $e) {
            throw new IOException("Set file attributes failed.", 0, $e);
        }

        $event = new FileEvent($file);
        $this->eventDispatcher->dispatch(MediaSiteEvents::SET_FILE_ATTRIBUTES, $event);

        return $file;
    }

    /**
     * @param FolderInterface $targetFolder
     * @param string          $name
     * @param AttributeBag    $attributes
     * @param string          $userId
     *
     * @return FolderInterface
     * @throws IOException
     */
    public function createFolder(FolderInterface $targetFolder, $name, AttributeBag $attributes, $userId)
    {
        $folderPath = rtrim($targetFolder->getPath(), '/') . '/' . $name;

        // prepare folder's name and id
        $folderClass = $this->folderClass;
        $folder = new $folderClass();
        /* @var $folder FolderInterface */
        $folder
            ->setSite($this->getSite())
            ->setId(Uuid::generate())
            ->setName($name)
            ->setParentId($targetFolder->getId())
            ->setPath($folderPath)
            ->setAttributes($attributes)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserid($userId)
            ->setModifiedAt($folder->getCreatedAt())
            ->setModifyUserid($folder->getCreateUserId());

        $this->validateCreateFolder($folder);

        $event = new FolderEvent($folder);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_CREATE_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Create folder {$folder->getName()} cancelled.");
        }

        try {
            $this->entityManager->persist($folder);
            $this->entityManager->flush($folder);
        } catch (\Exception $e) {
            throw new IOException("Create folder {$folder->getName()} failed", 0, $e);
        }

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(MediaSiteEvents::CREATE_FOLDER, $event);

        return $folder;
    }

    /**
     * @param FolderInterface $folder
     * @param string          $name
     * @param string          $userId
     *
     * @return FolderInterface
     * @throws IOException
     */
    public function renameFolder(FolderInterface $folder, $name, $userId)
    {
        $oldName = $folder->getName();
        $folder
            ->setName($name)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $this->validateRenameFolder($folder);

        $event = new RenameFolderEvent($folder, $oldName);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_RENAME_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Rename folder {$folder->getName()} cancelled.");
        }

        try {
            $this->entityManager->flush($folder);
        } catch (\Exception $e) {
            throw new IOException("Rename folder to {$folder->getName()} failed", 0, $e);
        }

        if (!$folder->isRoot()) {
            $oldPath = $folder->getPath();
            $parentFolder = $this->findFolder($folder->getParentId());
            $newPath = ltrim($parentFolder->getPath() . '/' . $name, '/');
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

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(MediaSiteEvents::RENAME_FOLDER, $event);

        return $folder;
    }

    /**
     * @param FolderInterface $folder
     * @param FolderInterface $targetFolder
     * @param string          $userId
     *
     * @return FolderInterface
     * @throws IOException
     */
    public function moveFolder(FolderInterface $folder, FolderInterface $targetFolder, $userId)
    {
        if ($folder->getParentId() === $targetFolder->getId()) {
            return null;
        }

        if ($folder->getId() === $targetFolder->getId()) {
            return null;
        }

        $newPath = rtrim($targetFolder->getPath(), '/') . '/' . $folder->getName();
        $oldPath = $folder->getPath();

        $folder
            ->setParentId($targetFolder->getId())
            ->setPath($newPath)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        $this->validateMoveFolder($folder);

        $event = new MoveFolderEvent($folder, $targetFolder);
        if ($this->eventDispatcher->dispatch(MediaSiteEvents::BEFORE_MOVE_FOLDER, $event)->isPropagationStopped()) {
            throw new IOException("Move folder {$folder->getName()} cancelled.");
        }

        try {
            $this->entityManager->flush($folder);

            $qb = $this->getFolderRepository()->createQueryBuilder('fo');

            if ($targetFolder->isRoot()) {
                $pathExpression = 'CONCAT(' . $qb->expr()->literal($targetFolder->getPath()) . ', path)';
            } else {
                $pathExpression = 'REPLACE(path, ' . $qb->expr()->literal($targetFolder->getPath()) . ', ' . $qb->expr()->literal($targetFolder->getPath()) . ')';
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

        $event = new FolderEvent($folder);
        $this->eventDispatcher->dispatch(MediaSiteEvents::MOVE_FOLDER, $event);

        return $folder;
    }

    /**
     * @param FolderInterface $folder
     * @param FolderInterface $targetFolder
     * @param string          $userId
     *
     * @return FolderInterface
     */
    public function copyFolder(FolderInterface $folder, FolderInterface $targetFolder, $userId)
    {
        $this->validateCopyFolder($folder, $targetFolder);

        $copiedFolder = $this->createFolder($targetFolder, $folder->getName() . uniqid(), $folder->getAttributes(), $userId);

        foreach ($this->findFoldersByParentFolder($folder) as $subFolder) {
            $this->copyFolder($subFolder, $copiedFolder, $userId);
        }

        foreach ($this->findFilesByFolder($folder) as $file) {
            $fileSource = new FilesystemFileSource($file->getPhysicalPath(), $file->getMimeType(), $file->getSize());
            $this->createFile($copiedFolder, $fileSource, $file->getAttributes(), $userId);
        }

        return $folder;
    }

    /**
     * @param FolderInterface $folder
     * @param string          $userId
     *
     * @return FolderInterface
     * @throws IOException
     */
    public function deleteFolder(FolderInterface $folder, $userId)
    {
        foreach ($this->findFoldersByParentFolder($folder) as $subFolder) {
            $this->deleteFolder($subFolder, $userId);
        }

        foreach ($this->findFilesByFolder($folder) as $file) {
            $this->deleteFile($file, $userId);
        }

        try {
            $this->entityManager->remove($folder);

            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new IOException("Delete folder failed.", 0, $e);
        }

        return $folder;
    }

    /**
     * @param FolderInterface $folder
     * @param AttributeBag    $attributes
     * @param string          $userId
     *
     * @return FolderInterface
     * @throws IOException
     */
    public function setFolderAttributes(FolderInterface $folder, AttributeBag $attributes, $userId)
    {
        $folder
            ->setAttributes($attributes)
            ->setModifiedAt(new \DateTime())
            ->setModifyUserId($userId);

        try {
            $this->entityManager->flush($folder);
        } catch (\Exception $e) {
            throw new IOException("Set folder attributes failed.", 0, $e);
        }

        return $folder;
    }

    /**
     * @param FolderInterface $folder
     *
     * @throws AlreadyExistsException
     */
    private function validateCreateFolder(FolderInterface $folder)
    {
        $targetFolder = $this->findFolder($folder->getParentId());
        $folderPath = trim($targetFolder->getPath() . '/' . $folder->getName(), '/');

        if ($this->findFolderByPath($folderPath)) {
            throw new AlreadyExistsException("Folder {$folderPath} already exists.");
        }
    }

    /**
     * @param FolderInterface $folder
     *
     * @throws AlreadyExistsException
     */
    private function validateRenameFolder(FolderInterface $folder)
    {
        $targetFolder = $this->findFolder($folder->getParentId());
        $folderPath = trim($targetFolder->getPath() . '/' . $folder->getName(), '/');

        if ($this->findFolderByPath($folderPath)) {
            throw new AlreadyExistsException("Folder {$folderPath} already exists.");
        }
    }

    /**
     * @param FolderInterface $folder
     *
     * @throws AlreadyExistsException
     */
    private function validateMoveFolder(FolderInterface $folder)
    {
        $targetFolder = $this->findFolder($folder->getParentId());
        $folderPath = trim($targetFolder->getPath() . '/' . $folder->getName(), '/');

        if ($this->findFolderByPath($folderPath)) {
            throw new AlreadyExistsException("Folder {$folderPath} already exists.");
        }
    }

    /**
     * @param FolderInterface $folder
     * @param FolderInterface $targetFolder
     *
     * @throws AlreadyExistsException
     */
    private function validateCopyFolder(FolderInterface $folder, FolderInterface $targetFolder)
    {
        $folderPath = trim($targetFolder->getPath() . '/' . $folder->getName(), '/');

        if ($this->findFolderByPath($folderPath)) {
            throw new AlreadyExistsException("Folder {$folderPath} already exists.");
        }
    }

    /**
     * @param FileInterface $file
     *
     * @throws AlreadyExistsException
     */
    private function validateCreateFile(FileInterface $file)
    {
        $filePath = $file->getFolder()->getPath() . '/' . $file->getName();
        if ($this->findFileByPath($filePath)) {
            throw new AlreadyExistsException("File {$filePath} already exists.");
        }
    }

    /**
     * @param FileInterface $file
     *
     * @throws AlreadyExistsException
     */
    private function validateRenameFile(FileInterface $file)
    {
        $filePath = $file->getFolder()->getPath() . '/' . $file->getName();
        if ($this->findFileByPath($filePath)) {
            throw new AlreadyExistsException("File {$filePath} already exists.");
        }
    }

    /**
     * @param FileInterface $file
     *
     * @throws AlreadyExistsException
     */
    private function validateMoveFile(FileInterface $file)
    {
        $filePath = $file->getFolder()->getPath() . '/' . $file->getName();
        if ($this->findFileByPath($filePath)) {
            throw new AlreadyExistsException("File {$filePath} already exists.");
        }
    }

    /**
     * @param FileInterface $file
     *
     * @throws AlreadyExistsException
     */
    private function validateCopyFile(FileInterface $file)
    {
        $filePath = $file->getFolder()->getPath() . '/' . $file->getName();
        if ($this->findFileByPath($filePath)) {
            throw new AlreadyExistsException("File {$filePath} already exists.");
        }
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
