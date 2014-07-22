<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Upload;

use Brainbits\Mime\MimeDetector;
use Phlexible\Bundle\MediaSiteBundle\FileSource\UploadedFileSource;
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Upload handler
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UploadHandler
{
    /**
     * @var SiteManager
     */
    private $siteManager;

    /**
     * @var TempStorage
     */
    private $tempStorage;

    /**
     * @var MimeDetector
     */
    private $mimeDetector;

    /**
     * @param SiteManager  $siteManager
     * @param TempStorage  $tempStorage
     * @param MimeDetector $mimeDetector
     */
    public function __construct(SiteManager $siteManager, TempStorage $tempStorage, MimeDetector $mimeDetector)
    {
        $this->siteManager = $siteManager;
        $this->tempStorage = $tempStorage;
        $this->mimeDetector = $mimeDetector;
    }

    /**
     * @return bool
     */
    private function useWizard()
    {
        return false;
    }

    /**
     * Handle upload
     *
     * @param UploadedFile $uploadedFile
     * @param string       $folderId
     * @param string       $userId
     *
     * @return TempFile|FileInterface
     */
    public function handle(UploadedFile $uploadedFile, $folderId, $userId)
    {
        $mimetype = $this->mimeDetector->detect($uploadedFile->getPathname(), MimeDetector::RETURN_STRING);
        $uploadFileSource = new UploadedFileSource($uploadedFile, $mimetype);

        $site = $this->siteManager->getByFolderId($folderId);
        $folder = $site->findFolder($folderId);
        $file = $site->findFileByPath($folder->getPath() . '/' . $uploadFileSource->getName());
        $originalFileId = null;

        if ($file) {
            $originalFileId = $file->getId();
        }

        $useWizard = $this->useWizard();

        if ($originalFileId || $useWizard) {
            return $this->tempStorage->store(
                $uploadFileSource,
                $folderId,
                $userId,
                $originalFileId,
                $useWizard
            );
        }

        $file = $site->createFile($folder, $uploadFileSource, new AttributeBag(), $userId);

        return $file;
    }
}
