<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Upload;

use Phlexible\Bundle\MediaSiteBundle\Site\SiteManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Upload temp storage
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TempHandler
{
    const ACTION_SAVE = 'save';
    const ACTION_REPLACE = 'replace';
    const ACTION_KEEP_BOtH = 'keep';
    const ACTION_DISCARD = 'discard';
    const ACTION_VERSION = 'version';

    /**
     * @var TempStorage
     */
    private $tempStorage;

    /**
     * @var SiteManager
     */
    private $siteManager;

    /**
     * @param TempStorage $tempStorage
     * @param SiteManager $siteManager
     */
    public function __construct(TempStorage $tempStorage, SiteManager $siteManager)
    {
        $this->tempStorage = $tempStorage;
        $this->siteManager = $siteManager;
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
        $site = $this->siteManager->getByFolderId($tempFile->getFolderId());
        $folder = $site->findFolder($tempFile->getFolderId());

        switch ($action) {
            case self::ACTION_SAVE:
                $site->createFile($folder, $tempFile, $tempFile->getUserId());
                break;

            case self::ACTION_REPLACE:
                $file = $site->findFile($tempFile->getFileId());
                $site->replaceFile($file, $tempFile, $tempFile->getUserId());
                break;

            case self::ACTION_KEEP_BOtH:
                $tempFile->setAlternativeName($this->createAlternateFilename($tempFile));
                $site->createFile($folder, $tempFile, $tempFile->getUserId());
                break;

            case self::ACTION_VERSION:
                die('fix me');
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
        $site = $this->siteManager->getByFolderId($tempFile->getFolderId());
        $folder = $site->findFolder($tempFile->getFolderId());

        $newNameParts = pathinfo($tempFile->getName());
        $newNameFormat = basename($newNameParts['basename'], '.' . $newNameParts['extension']);
        $newNameFormat .= '(%s).' . $newNameParts['extension'];

        $i = 0;

        do {
            $i++;
            $newName = sprintf($newNameFormat, $i);
            $testFilename = $folder->getPath() . '/' . $newName;
        } while ($site->findFileByPath($testFilename));

        return $newName;
    }
}
