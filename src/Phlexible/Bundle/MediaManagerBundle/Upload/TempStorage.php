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
    public function all()
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
        if (!$this->session->has('mediamanager.temp_files')) {
            return 0;
        }

        return count($this->session->get('mediamanager.temp_files'));
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id)
    {
        if (!$this->count()) {
            return false;
        }

        $tempFiles = $this->session->get('mediamanager.temp_files');

        return isset($tempFiles[$id]);
    }

    /**
     * @param string $id
     *
     * @return TempFile|null
     */
    public function get($id)
    {
        if ($this->has($id)) {
            $tempFiles = $this->all();

            return $tempFiles[$id];
        }

        return null;
    }

    /**
     * @return TempFile|null
     */
    public function next()
    {
        if (!$this->count()) {
            return null;
        }

        return current($this->session->get('mediamanager.temp_files'));
    }

    /**
     * @return $this
     */
    public function removeAll()
    {
        if ($this->session->has('mediamanager.temp_files')) {
            $this->session->remove('mediamanager.temp_files');
        }

        return $this;
    }

    /**
     * @param TempFile $file
     *
     * @return $this
     */
    public function remove(TempFile $file)
    {
        // TODO: cleanup file?

        if ($this->session->has('mediamanager.temp_files') && isset($this->session->get('mediamanager.temp_files')[$file->getId()])) {
            $tempFiles = $this->session->get('mediamanager.temp_files');
            unset($tempFiles[$file->getId()]);
            $this->session->set('mediamanager.temp_files', $tempFiles);
        }

        return $this;
    }

    /**
     * Store upload
     *
     * @param UploadedFileSource $file
     * @param string             $folderId
     * @param int                $userId
     * @param string             $originalFileId
     * @param bool               $useWizard
     *
     * @return TempFile
     * @throws \Exception
     */
    public function store(UploadedFileSource $file, $folderId, $userId, $originalFileId, $useWizard)
    {
        $tempId = uniqid();
        $tempDir = $this->tempDir . '/' . $tempId . '/';
        $tempName = $tempDir . basename($file->getPath());

        if (!file_exists($tempDir) && !mkdir($tempDir, 0777, true)) {
            throw new \Exception('Error occured while creating temp upload folder.');
        }

        if (!move_uploaded_file($file->getPath(), $tempName)) {
            throw new \Exception('Error occured during uploaded file move.');
        }

        $tempFile = new TempFile(
            $tempId,
            $file->getName(),
            $tempName,
            $file->getMimeType(),
            $file->getSize(),
            $originalFileId,
            $folderId,
            $userId,
            $useWizard
        );

        /*
        $event = new BeforeStoreUploadEvent($this, $tempFile);
        if ($this->dispatcher->dispatch(MediaSiteEvents::BEFORE_STORE_UPLOAD, $event)->isPropagationStopped()) {
            return null;
        }
        */

        $tempFiles = $this->session->get('mediamanager.temp_files');
        $tempFiles[$tempId] = $tempFile;
        $this->session->set('mediamanager.temp_files', $tempFiles);

        /*
        $event = new StoreUploadEvent($this, $tempFile);
        $this->dispatcher->dispatch(MediaSiteEvents::STORE_UPLOAD, $event);
        */

        return $tempFile;
    }
}
