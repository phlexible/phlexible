<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Upload;

use Phlexible\Bundle\MediaSiteBundle\FileSource\UploadedFileSource;
use Phlexible\Bundle\MediaSiteBundle\MediaSiteEvents;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Upload temp storage
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TempStorage
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $tempDir;

    /**
     * @param SessionInterface $session
     * @param string           $tempDir
     */
    public function __construct(SessionInterface $session, $tempDir)
    {
        $this->session = $session;
        $this->tempDir = $tempDir;
    }

    /**
     * @return TempFile[]
     */
    public function getAll()
    {
        if (!$this->count()) {
            return array();
        }

        return $this->session->get('mediamanager.temp_files');
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->session->has('mediamanager.temp_files') && count($this->session->get('mediamanager.temp_files'));
    }

    /**
     * @return TempFile|null
     */
    public function getNext()
    {
        if (!$this->count()) {
            return null;
        }

        return current($this->session->get('mediamanager.temp_files'));
    }

    /**
     *
     */
    public function removeAll()
    {
        if ($this->session->has('mediamanager.temp_files')) {
            $this->session->remove('mediamanager.temp_files');
        }
    }

    /**
     * @param TempFile $file
     */
    public function remove(TempFile $file)
    {
        if ($this->session->has('mediamanager.temp_files') && isset($this->session->get('mediamanager.temp_files')[$file->getId()])) {
            unset($this->session->has('mediamanager.temp_files')[$file->getId()]);
        }
    }

    /**
     * Add upload to temp storage
     *
     * @param UploadedFileSource $file
     * @param string           $folderId
     * @param int              $uid
     * @param string           $originalFileId
     * @param bool             $useWizard
     *
     * @return TempFile
     * @throws \Exception
     */
    public function addUploadFile(UploadedFileSource $file, $folderId, $uid, $originalFileId, $useWizard)
    {
        $tempId = uniqid();
        $tempDir = $this->tempDir . '/' . $tempId . '/';
        $tempName = $tempDir . basename($file->getTempName());

        if (!file_exists($tempDir) && !mkdir($tempDir, 0777, true)) {
            throw new \Exception('Error occured while creating temp upload folder.');
        }

        if (!move_uploaded_file($file->getTempName(), $tempName)) {
            throw new \Exception('Error occured during uploaded file move.');
        }

        $tempFile = new TempFile($file->getName(), $tempName, $file->getType(), $file->getSize(), 0);
        $tempFile
            ->setId($tempId)
            ->setOriginalFileId($originalFileId)
            ->setFolderId($folderId)
            ->setWizard($useWizard)
            ->setUserId($uid);

        $event = new BeforeStoreUploadEvent($this, $tempFile);
        if ($this->dispatcher->dispatch(MediaSiteEvents::BEFORE_STORE_UPLOAD, $event) === false) {
            return null;
        }

        $this->session[$tempId] = $tempFile;

        $event = new StoreUploadEvent($this, $tempFile);
        $this->dispatcher->dispatch(MediaSiteEvents::STORE_UPLOAD, $event);

        return $tempFile;
    }
}
