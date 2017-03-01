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

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Phlexible\Component\Mime\MimeDetector;
use Phlexible\Component\Volume\FileSource\UploadedFileSource;
use Phlexible\Component\Volume\VolumeManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Upload handler.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UploadHandler
{
    /**
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * @var TempStorage
     */
    private $tempStorage;

    /**
     * @var MimeDetector
     */
    private $mimeDetector;

    /**
     * @var MediaTypeManagerInterface
     */
    private $mediaTypeManager;

    /**
     * @var array|null
     */
    private $wizardCategories;

    /**
     * @var array|null
     */
    private $wizardTypes;

    /**
     * @param VolumeManager             $volumeManager
     * @param TempStorage               $tempStorage
     * @param MimeDetector              $mimeDetector
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param array|null                $wizardCategories
     * @param array|null                $wizardTypes
     */
    public function __construct(
        VolumeManager $volumeManager,
        TempStorage $tempStorage,
        MimeDetector $mimeDetector,
        MediaTypeManagerInterface $mediaTypeManager,
        array $wizardCategories = null,
        array $wizardTypes = null
    ) {
        $this->volumeManager = $volumeManager;
        $this->tempStorage = $tempStorage;
        $this->mimeDetector = $mimeDetector;
        $this->mediaTypeManager = $mediaTypeManager;
        $this->wizardCategories = $wizardCategories;
        $this->wizardTypes = $wizardTypes;
    }

    /**
     * @param UploadedFileSource $uploadFileSource
     *
     * @return bool
     */
    private function useWizard(UploadedFileSource $uploadFileSource)
    {
        $newType = $this->mediaTypeManager->findByMimetype($uploadFileSource->getMimeType());

        if (!$newType) {
            return false;
        }

        if ($this->wizardCategories !== null && in_array($newType->getCategory(), $this->wizardCategories)) {
            return true;
        }

        if ($this->wizardTypes !== null && in_array($newType->getName(), $this->wizardTypes)) {
            return true;
        }

        return false;
    }

    /**
     * Handle upload.
     *
     * @param UploadedFile $uploadedFile
     * @param string       $folderId
     * @param string       $userId
     *
     * @return TempFile|ExtendedFileInterface
     */
    public function handle(UploadedFile $uploadedFile, $folderId, $userId)
    {
        $mimetype = $this->mimeDetector->detect($uploadedFile->getPathname(), MimeDetector::RETURN_STRING);
        $uploadFileSource = new UploadedFileSource($uploadedFile, $mimetype);

        $volume = $this->volumeManager->getByFolderId($folderId);
        $folder = $volume->findFolder($folderId);
        $file = $volume->findFileByPath($folder->getPath().'/'.$uploadFileSource->getName());
        $originalFileId = null;

        if ($file) {
            $originalFileId = $file->getId();
        }

        $useWizard = $this->useWizard($uploadFileSource);

        if ($originalFileId || $useWizard) {
            return $this->tempStorage->store(
                $uploadFileSource,
                $folderId,
                $userId,
                $originalFileId,
                $useWizard
            );
        }

        $file = $volume->createFile($folder, $uploadFileSource, array(), $userId);

        return $file;
    }
}
