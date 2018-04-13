<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\Upload;

use Phlexible\Component\Volume\Model\FileInterface;
use Phlexible\Component\Volume\VolumeManager;

/**
 * Upload temp storage.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TempHandler
{
    const ACTION_SAVE = 'save';
    const ACTION_REPLACE = 'replace';
    const ACTION_KEEP_BOTH = 'keep';
    const ACTION_DISCARD = 'discard';
    const ACTION_SAVE_VERSION = 'save_version';

    /**
     * @var TempStorage
     */
    private $tempStorage;

    /**
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * @param TempStorage   $tempStorage
     * @param VolumeManager $volumeManager
     */
    public function __construct(TempStorage $tempStorage, VolumeManager $volumeManager)
    {
        $this->tempStorage = $tempStorage;
        $this->volumeManager = $volumeManager;
    }

    /**
     * @param string $action
     * @param string $id
     *
     * @return FileInterface|null
     */
    public function handle($action, $id)
    {
        $tempFile = $this->tempStorage->get($id);

        if (!$tempFile) {
            return null;
        }

        return $this->handleTempFile($action, $tempFile);
    }

    /**
     * @param string $action
     *
     * @return FileInterface[]
     */
    public function handleAll($action)
    {
        $files = array();
        foreach ($this->tempStorage->all() as $tempFile) {
            $file = $this->handleTempFile($action, $tempFile);
            if ($file) {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * @param string   $action
     * @param TempFile $tempFile
     *
     * @return FileInterface
     */
    private function handleTempFile($action, TempFile $tempFile)
    {
        $volume = $this->volumeManager->getByFolderId($tempFile->getFolderId());
        $folder = $volume->findFolder($tempFile->getFolderId());
        $file = null;

        switch ($action) {
            case self::ACTION_SAVE:
                $file = $volume->createFile($folder, $tempFile, array(), $tempFile->getUserId());
                break;

            case self::ACTION_REPLACE:
                $file = $volume->findFile($tempFile->getFileId());
                $volume->replaceFile($file, $tempFile, array(), $tempFile->getUserId());
                break;

            case self::ACTION_KEEP_BOTH:
                $tempFile->setAlternativeName($this->createAlternateFilename($tempFile));
                $file = $volume->createFile($folder, $tempFile, array(), $tempFile->getUserId());
                break;

            case self::ACTION_SAVE_VERSION:
                $file = $volume->findFile($tempFile->getFileId());
                $fileVersion = $volume->createFileVersion($file, $tempFile, array(), $tempFile->getUserId());
                $volume->activateFileVersion($file, $fileVersion, $tempFile->getUserId());
                break;

            case self::ACTION_DISCARD:
            default:
                break;
        }

        $this->tempStorage->remove($tempFile);

        return $file;
    }

    /**
     * @param TempFile $tempFile
     *
     * @return string
     */
    public function createAlternateFilename(TempFile $tempFile)
    {
        $volume = $this->volumeManager->getByFolderId($tempFile->getFolderId());
        $folder = $volume->findFolder($tempFile->getFolderId());

        $newNameParts = pathinfo($tempFile->getName());
        $newNameFormat = basename($newNameParts['basename'], '.'.$newNameParts['extension']);
        $newNameFormat .= '(%s).'.$newNameParts['extension'];

        $i = 0;

        do {
            ++$i;
            $newName = sprintf($newNameFormat, $i);
            $testFilename = $folder->getPath().'/'.$newName;
        } while ($volume->findFileByPath($testFilename));

        return $newName;
    }
}
