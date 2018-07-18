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

use Phlexible\Bundle\MediaManagerBundle\MetaSet\MediaTypeMatcher;
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
     * @var MediaTypeManagerInterface
     */
    private $mediaTypeManager;

    /**
     * @var MediaTypeMatcher
     */
    private $mediaTypeMatcher;

    /**
     * @param VolumeManager             $volumeManager
     * @param TempStorage               $tempStorage
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param MediaTypeMatcher          $mediaTypeMatcher
     */
    public function __construct(
        VolumeManager $volumeManager,
        TempStorage $tempStorage,
        MediaTypeManagerInterface $mediaTypeManager,
        MediaTypeMatcher $mediaTypeMatcher
    ) {
        $this->volumeManager = $volumeManager;
        $this->tempStorage = $tempStorage;
        $this->mediaTypeManager = $mediaTypeManager;
        $this->mediaTypeMatcher = $mediaTypeMatcher;
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

        if ($this->mediaTypeMatcher->match($newType)) {
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
        $uploadFileSource = new UploadedFileSource($uploadedFile);

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
