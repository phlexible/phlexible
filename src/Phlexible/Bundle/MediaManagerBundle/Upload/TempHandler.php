<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Upload;

use Phlexible\Component\Volume\VolumeManager;

/**
 * Upload temp storage
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TempHandler
{
    const ACTION_SAVE = 'save';
    const ACTION_REPLACE = 'replace';
    const ACTION_KEEP_BOTH = 'keep';
    const ACTION_DISCARD = 'discard';
    const ACTION_VERSION = 'version';

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
     */
    public function handle($action, $id)
    {
        $tempFile = $this->tempStorage->get($id);

        if (!$tempFile) {
            return;
        }

        $this->handleTempFile($action, $tempFile);
    }

    /**
     * @param string $action
     */
    public function handleAll($action)
    {
        foreach ($this->tempStorage->all() as $tempFile) {
            $this->handleTempFile($action, $tempFile);
        }
    }

    /**
     * @param string   $action
     * @param TempFile $tempFile
     */
    private function handleTempFile($action, TempFile $tempFile)
    {
        $volume = $this->volumeManager->getByFolderId($tempFile->getFolderId());
        $folder = $volume->findFolder($tempFile->getFolderId());

        switch ($action) {
            case self::ACTION_SAVE:
                $volume->createFile($folder, $tempFile, array(), $tempFile->getUserId());
                break;

            case self::ACTION_REPLACE:
                $file = $volume->findFile($tempFile->getFileId());
                $volume->replaceFile($file, $tempFile, array(), $tempFile->getUserId());
                break;

            case self::ACTION_KEEP_BOTH:
                $tempFile->setAlternativeName($this->createAlternateFilename($tempFile));
                $volume->createFile($folder, $tempFile, array(), $tempFile->getUserId());
                break;

            case self::ACTION_VERSION:
                throw new \LogicException('fix me');
                /*
                try {
                    $fileId = $tempFile['original_id'];
                    if (!empty($tempFile['file_id'])) {
                        $fileId = $tempFile['file_id'];
                    }

                    $newFile = $folder->importFileVersion($tempFile['tmp_name'], $tempFile['name'], $fileId);
                } catch (Exception $e) {
                    $this->getContainer()->get('logger')->error(
                        __METHOD__ . ' version: ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString()
                    );

                    throw new Media_Site_Folder_Exception($e->getMessage());
                }*/
                break;

            case self::ACTION_DISCARD:
            default:
                break;
        }

        $this->tempStorage->remove($tempFile);

        // TODO: add meta
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
        $newNameFormat = basename($newNameParts['basename'], '.' . $newNameParts['extension']);
        $newNameFormat .= '(%s).' . $newNameParts['extension'];

        $i = 0;

        do {
            $i++;
            $newName = sprintf($newNameFormat, $i);
            $testFilename = $folder->getPath() . '/' . $newName;
        } while ($volume->findFileByPath($testFilename));

        return $newName;
    }
}
