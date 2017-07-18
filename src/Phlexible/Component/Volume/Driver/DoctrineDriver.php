<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume\Driver;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Component\Volume\Exception\AlreadyExistsException;
use Phlexible\Component\Volume\Exception\IOException;
use Phlexible\Component\Volume\Exception\NotWritableException;
use Phlexible\Component\Volume\FileSource\FileSourceInterface;
use Phlexible\Component\Volume\FileSource\PathSourceInterface;
use Phlexible\Component\Volume\FileSource\StreamSourceInterface;
use Phlexible\Component\Volume\HashCalculator\HashCalculatorInterface;
use Phlexible\Component\Volume\Model\FileInterface;
use Phlexible\Component\Volume\Model\FolderInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Database driver.
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
     * @var string
     */
    private $folderClass;

    /**
     * @var string
     */
    private $fileClass;

    /**
     * @var array
     */
    private $features;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param EntityManager           $entityManager
     * @param HashCalculatorInterface $hashCalculator
     * @param string                  $folderClass
     * @param string                  $fileClass
     * @param array                   $features
     */
    public function __construct(EntityManager $entityManager, HashCalculatorInterface $hashCalculator, $folderClass, $fileClass, array $features = [])
    {
        $this->entityManager = $entityManager;
        $this->hashCalculator = $hashCalculator;
        $this->folderClass = $folderClass;
        $this->fileClass = $fileClass;
        $this->features = $features;

        $this->connection = $entityManager->getConnection();
    }

    /**
     * {@inheritdoc}
     */
    public function getFeatures()
    {
        return $this->features;
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
    public function getFileClass()
    {
        return $this->fileClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getFolderClass()
    {
        return $this->folderClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getHashCalculator()
    {
        return $this->hashCalculator;
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
            [
                'volumeId' => $this->getVolume()->getId(),
                'id' => $id,
            ]
        );

        if ($folder) {
            $folder->setVolume($this->getVolume());
        }

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function findRootFolder()
    {
        $folder = $this->getFolderRepository()->findOneBy(
            [
                'volumeId' => $this->getVolume()->getId(),
                'parentId' => null,
            ]
        );

        if ($folder) {
            $folder->setVolume($this->getVolume());
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
            [
                'volumeId' => $this->getVolume()->getId(),
                'path' => $path,
            ]
        );

        if ($folder) {
            $folder->setVolume($this->getVolume());
        }

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function findFoldersByParentFolder(FolderInterface $parentFolder)
    {
        $folders = $this->getFolderRepository()->findBy(
            [
                'parentId' => $parentFolder->getId(),
            ]
        );

        foreach ($folders as $folder) {
            /* @var $folder FolderInterface */
            $folder->setVolume($this->getVolume());
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
        $folder->setVolume($this->getVolume());

        return $folder;
    }

    /**
     * {@inheritdoc}
     */
    public function findFile($id, $version = 1)
    {
        $file = $this->getFileRepository()->findOneBy(
            [
                'id' => $id,
                'version' => $version,
            ]
        );

        if ($file) {
            $file->setVolume($this->getVolume());
        }

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function findFiles(array $criteria, $order = null, $limit = null, $start = null, $includeHidden = false)
    {
        $qb = $this->getFileRepository()->createQueryBuilder('fi');

        foreach ($criteria as $field => $value) {
            switch ($field) {
                case 'folder':
                    $qb->andWhere($qb->expr()->eq('fi.folder', $qb->expr()->literal($value->getId())));
                    break;

                case 'mediaCategory':
                    $qb->andWhere($qb->expr()->eq('fi.mediaCategory', $qb->expr()->literal($value)));
                    break;

                case 'mediaTypes':
                    $qb->andWhere($qb->expr()->in('fi.mediaType', explode(',', $value)));
                    break;

                case 'timeCreated':
                    $qb->andWhere($qb->expr()->gte('fi.createdAt', $qb->expr()->literal(\DateTime::createFromFormat('U', $value)->format('Y-m-d H:i:s'))));
                    break;

                case 'timeModified':
                    $qb->andWhere($qb->expr()->gte('fi.modifiedAt', $qb->expr()->literal(\DateTime::createFromFormat('U', $value)->format('Y-m-d H:i:s'))));
                    break;

                case 'hidden':
                    $qb->andWhere($qb->expr()->eq('fi.hidden', $value ? 1 : 0));
                    break;

                case 'notCreateUserId':
                    $qb->andWhere($qb->expr()->neq('fi.createUserId', $qb->expr()->literal($value)));
                    break;

                case 'notModifyUserId':
                    $qb->andWhere($qb->expr()->neq('fi.modifyUserId', $qb->expr()->literal($value)));
                    break;

                default:
                    $qb->andWhere($qb->expr()->eq("fi.$field", $qb->expr()->literal($value)));
            }
        }

        if ($order) {
            foreach ($order as $field => $dir) {
                $qb->addOrderBy("fi.$field", $dir);
            }
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        if ($start) {
            $qb->setFirstResult($start);
        }

        $files = $qb->getQuery()->getResult();

        foreach ($files as $file) {
            $file->setVolume($this->getVolume());
        }

        return $files;
    }

    /**
     * {@inheritdoc}
     */
    public function countFiles(array $criteria)
    {
        $qb = $this->getFileRepository()->createQueryBuilder('fi');
        $qb->select('COUNT(fi.id)');

        foreach ($criteria as $field => $value) {
            switch ($field) {
                case 'folder':
                    $qb->andWhere($qb->expr()->eq('fi.folder', $qb->expr()->literal($value->getId())));
                    break;

                case 'mediaCategory':
                    $qb->andWhere($qb->expr()->eq('fi.mediaCategory', $qb->expr()->literal($value)));
                    break;

                case 'mediaTypes':
                    $qb->andWhere($qb->expr()->in('fi.mediaType', explode(',', $value)));
                    break;

                case 'timeCreated':
                    $qb->andWhere($qb->expr()->gte('fi.createdAt', $qb->expr()->literal(\DateTime::createFromFormat('U', $value)->format('Y-m-d H:i:s'))));
                    break;

                case 'timeModified':
                    $qb->andWhere($qb->expr()->gte('fi.modifiedAt', $qb->expr()->literal(\DateTime::createFromFormat('U', $value)->format('Y-m-d H:i:s'))));
                    break;

                case 'hidden':
                    $qb->andWhere($qb->expr()->eq('fi.hidden', $value ? 1 : 0));
                    break;

                case 'notCreateUserId':
                    $qb->andWhere($qb->expr()->neq('fi.createUserId', $qb->expr()->literal($value)));
                    break;

                case 'notModifyUserId':
                    $qb->andWhere($qb->expr()->neq('fi.modifyUserId', $qb->expr()->literal($value)));
                    break;

                default:
                    $qb->andWhere($qb->expr()->eq("fi.$field", $qb->expr()->literal($value)));
            }
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
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
            [
                'name' => $name,
                'folder' => $folder,
            ]
        );

        if ($file) {
            $file->setVolume($this->getVolume());
        }

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function findFileVersions($id)
    {
        $files = $this->getFileRepository()->findBy(
            [
                'id' => $id,
            ]
        );

        foreach ($files as $file) {
            /* @var $file FileInterface */
            $file->setVolume($this->getVolume());
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
        $criteria = [
            'folder' => $folder,
        ];

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
            $file->setVolume($this->getVolume());
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
        $files = $this->getFileRepository()->findBy([], ['createdAt' => 'DESC'], $limit);

        foreach ($files as $file) {
            /* @var $file FileInterface */
            $file->setVolume($this->getVolume());
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
            $file->setVolume($this->getVolume());
        }

        return $files;
    }

    /**
     * {@inheritdoc}
     */
    public function updateFile(FileInterface $file)
    {
        $this->entityManager->persist($file);
        $this->entityManager->flush($file);
    }

    /**
     * {@inheritdoc}
     */
    public function createFile(FileInterface $file, FileSourceInterface $fileSource)
    {
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
            $this->updateFile($file);
        } catch (\Exception $e) {
            $filesystem->remove($path);

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function replaceFile(FileInterface $file, FileSourceInterface $fileSource)
    {
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
            $this->updateFile($file);
        } catch (\Exception $e) {
            $filesystem->remove($path);

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile(FileInterface $file)
    {
        $this->entityManager->remove($file);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function hideFile(FileInterface $file)
    {
        $file->setHidden(true);
        $this->updateFile($file);
    }

    /**
     * {@inheritdoc}
     */
    public function showFile(FileInterface $file)
    {
        $file->setHidden(false);
        $this->updateFile($file);
    }

    /**
     * {@inheritdoc}
     */
    public function updateFolder(FolderInterface $folder)
    {
        $this->entityManager->persist($folder);
        $this->entityManager->flush($folder);
    }

    /**
     * {@inheritdoc}
     */
    public function renameFolder(FolderInterface $folder, $oldPath)
    {
        $this->updateFolder($folder);

        if ($folder->isRoot()) {
            return;
        }

        $oldPath = rtrim($oldPath, '/').'/';
        $replacePath = rtrim($folder->getPath(), '/').'/';

        $qb = $this->getFolderRepository()->createQueryBuilder('fo');
        $qb
            ->where($qb->expr()->eq('fo.volumeId', $qb->expr()->literal($this->getVolume()->getId())))
            ->andWhere(
                $qb->expr()->eq(
                    $qb->expr()->substring('fo.path', 1, mb_strlen($oldPath)),
                    $qb->expr()->literal($oldPath)
                )
            );

        foreach ($qb->getQuery()->getResult() as $subFolder) {
            /* @var FolderInterface $subFolder */
            $subFolder->setPath(str_replace($oldPath, $replacePath, $subFolder->getPath()));
            $this->updateFolder($subFolder);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function moveFolder(FolderInterface $folder, $oldPath)
    {
        $this->updateFolder($folder);

        $oldPath = rtrim($oldPath, '/').'/';
        $replacePath = rtrim($folder->getPath(), '/').'/';

        $qb = $this->getFolderRepository()->createQueryBuilder('fo');
        $qb
            ->where($qb->expr()->eq('fo.volumeId', $qb->expr()->literal($this->getVolume()->getId())))
            ->andWhere(
                $qb->expr()->eq(
                    $qb->expr()->substring('fo.path', 1, mb_strlen($oldPath)),
                    $qb->expr()->literal($oldPath)
                )
            );

        foreach ($qb->getQuery()->getResult() as $subFolder) {
            /* @var FolderInterface $subFolder */
            $subFolder->setPath(str_replace($oldPath, $replacePath, $subFolder->getPath()));
            $this->updateFolder($subFolder);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFolder(FolderInterface $folder)
    {
        $this->entityManager->remove($folder);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function validateCreateFolder(FolderInterface $folder)
    {
        if ($folder->getParentId()) {
            $targetFolder = $this->findFolder($folder->getParentId());
            $folderPath = trim($targetFolder->getPath().'/'.$folder->getName(), '/');
        } else {
            $folderPath = '';
        }

        if ($this->findFolderByPath($folderPath)) {
            throw new AlreadyExistsException("Folder {$folderPath} already exists.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateRenameFolder(FolderInterface $folder)
    {
        $targetFolder = $this->findFolder($folder->getParentId());
        $folderPath = trim($targetFolder->getPath().'/'.$folder->getName(), '/');

        if ($this->findFolderByPath($folderPath)) {
            throw new AlreadyExistsException("Folder {$folderPath} already exists.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateMoveFolder(FolderInterface $folder)
    {
        $targetFolder = $this->findFolder($folder->getParentId());
        $folderPath = trim($targetFolder->getPath().'/'.$folder->getName(), '/');

        if ($this->findFolderByPath($folderPath)) {
            throw new AlreadyExistsException("Folder {$folderPath} already exists.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateCopyFolder(FolderInterface $folder, FolderInterface $targetFolder)
    {
        $folderPath = trim($targetFolder->getPath().'/'.$folder->getName(), '/');

        if ($this->findFolderByPath($folderPath)) {
            throw new AlreadyExistsException("Folder {$folderPath} already exists.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateCreateFile(FileInterface $file, FolderInterface $folder)
    {
        $filePath = $file->getFolder()->getPath().'/'.$file->getName();
        if ($this->findFileByPath($filePath)) {
            throw new AlreadyExistsException("File {$filePath} already exists.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateRenameFile(FileInterface $file, FolderInterface $folder)
    {
        $this->validateMoveFile($file, $folder);
    }

    /**
     * {@inheritdoc}
     */
    public function validateMoveFile(FileInterface $file, FolderInterface $folder)
    {
        $filePath = $folder->getPath().'/'.$file->getName();
        if ($this->findFileByPath($filePath)) {
            throw new AlreadyExistsException("File {$filePath} already exists.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateCopyFile(FileInterface $file, FolderInterface $folder)
    {
        $this->validateMoveFile($file, $folder);
    }

    /**
     * @param FolderInterface $folder
     * @param string          $userId
     *
     * @throws NotWritableException
     * @throws IOException
     */
    public function deletePhysicalFolder(FolderInterface $folder, $userId)
    {
        $filesystem = new Filesystem();

        $physicalPath = $this->getVolume()->getRootDir().$folder->getPath();

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
