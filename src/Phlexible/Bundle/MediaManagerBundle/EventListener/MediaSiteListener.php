<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\EventListener;

use Phlexible\Bundle\MediaManagerBundle\Volume\DeleteFileChecker;
use Phlexible\Bundle\MediaManagerBundle\Volume\DeleteFolderChecker;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Bundle\MetaSetBundle\Model\MetaSetManagerInterface;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Phlexible\Component\Volume\Event\CreateFileEvent;
use Phlexible\Component\Volume\Event\FileEvent;
use Phlexible\Component\Volume\Event\FolderEvent;
use Phlexible\Component\Volume\Event\ReplaceFileEvent;
use Phlexible\Component\Volume\FileSource\PathSourceInterface;
use Phlexible\Component\Volume\VolumeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Media site listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaSiteListener implements EventSubscriberInterface
{
    /**
     * @var MediaTypeManagerInterface
     */
    private $mediaTypeManager;

    /**
     * @var MetaSetManagerInterface
     */
    private $metaSetManager;

    /**
     * @var DeleteFileChecker
     */
    private $deleteFileChecker;

    /**
     * @var DeleteFolderChecker
     */
    private $deleteFolderChecker;

    /**
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param MetaSetManagerInterface   $metaSetManager
     * @param DeleteFileChecker         $deleteFileChecker
     * @param DeleteFolderChecker       $deleteFolderChecker
     */
    public function __construct(
        MediaTypeManagerInterface $mediaTypeManager,
        MetaSetManagerInterface $metaSetManager,
        DeleteFileChecker $deleteFileChecker,
        DeleteFolderChecker $deleteFolderChecker)
    {
        $this->mediaTypeManager = $mediaTypeManager;
        $this->metaSetManager = $metaSetManager;
        $this->deleteFileChecker = $deleteFileChecker;
        $this->deleteFolderChecker = $deleteFolderChecker;
    }


    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            VolumeEvents::BEFORE_CREATE_FILE   => ['onBeforeCreateFile', 500],
            VolumeEvents::BEFORE_CREATE_FOLDER => ['onBeforeCreateFolder', 500],
            VolumeEvents::BEFORE_REPLACE_FILE   => ['onBeforeReplaceFile', 500],
            VolumeEvents::BEFORE_DELETE_FILE   => 'onBeforeDeleteFile',
            VolumeEvents::BEFORE_DELETE_FOLDER => 'onBeforeDeleteFolder',
        ];
    }

    /**
     * @param CreateFileEvent $event
     */
    public function onBeforeCreateFile(CreateFileEvent $event)
    {
        $file = $event->getFile();
        $fileSource = $event->getFileSource();

        $this->processFile($file, $fileSource);
    }

    /**
     * @param ReplaceFileEvent $event
     */
    public function onBeforeReplaceFile(ReplaceFileEvent $event)
    {
        $file = $event->getFile();
        $fileSource = $event->getFileSource();

        $this->processFile($file, $fileSource);
    }

    /**
     * @param ExtendedFileInterface $file
     * @param PathSourceInterface   $fileSource
     */
    private function processFile(ExtendedFileInterface $file, PathSourceInterface $fileSource)
    {
        try {
            $mediaType = $this->mediaTypeManager->findByMimetype($fileSource->getMimeType());
        } catch (\Exception $e) {
            $mediaType = $this->mediaTypeManager->find('binary');
        }

        $file->setMediaType($mediaType->getName());

        try {
            $fileMetaSet = $this->metaSetManager->findOneByName('file');
            if ($fileMetaSet) {
                $file->addMetaSet($fileMetaSet->getId());
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @param FolderEvent $event
     */
    public function onBeforeCreateFolder(FolderEvent $event)
    {
        $folder = $event->getFolder();

        try {
            $folderMetaSet = $this->metaSetManager->findOneByName('folder');
            if ($folderMetaSet) {
                $folder->addMetaset($folderMetaSet->getId());
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @param FileEvent $event
     */
    public function onBeforeDeleteFile(FileEvent $event)
    {
        if (!$this->deleteFileChecker->isDeleteAllowed($event->getFile())) {
            $event->stopPropagation();
        }
    }

    /**
     * @param FolderEvent $event
     */
    public function onBeforeDeleteFolder(FolderEvent $event)
    {
        if (!$this->deleteFolderChecker->isDeleteAllowed($event->getFolder())) {
            $event->stopPropagation();
        }
    }
}
