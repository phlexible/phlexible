<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\EventListener;

use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeManagerInterface;
use Phlexible\Bundle\MediaManagerBundle\Site\DeleteFileChecker;
use Phlexible\Bundle\MediaManagerBundle\Site\DeleteFolderChecker;
use Phlexible\Bundle\MediaManagerBundle\Site\ExtendedFileInterface;
use Phlexible\Bundle\MediaSiteBundle\Event\CreateFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\FileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\FolderEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\ReplaceFileEvent;
use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\MediaSiteEvents;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MetaSetBundle\Model\MetaSetManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Media site listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaSiteListener implements EventSubscriberInterface
{
    /**
     * @var DocumenttypeManagerInterface
     */
    private $documenttypeManager;

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
     * @param DocumenttypeManagerInterface $documenttypeManager
     * @param MetaSetManagerInterface      $metaSetManager
     * @param DeleteFileChecker            $deleteFileChecker
     * @param DeleteFolderChecker          $deleteFolderChecker
     */
    public function __construct(
        DocumenttypeManagerInterface $documenttypeManager,
        MetaSetManagerInterface $metaSetManager,
        DeleteFileChecker $deleteFileChecker,
        DeleteFolderChecker $deleteFolderChecker)
    {
        $this->documenttypeManager = $documenttypeManager;
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
            MediaSiteEvents::BEFORE_CREATE_FILE   => ['onBeforeCreateFile', 500],
            MediaSiteEvents::BEFORE_CREATE_FOLDER => ['onBeforeCreateFolder', 500],
            MediaSiteEvents::BEFORE_REPLACE_FILE   => ['onBeforeReplaceFile', 500],
            MediaSiteEvents::BEFORE_DELETE_FILE   => 'onBeforeDeleteFile',
            MediaSiteEvents::BEFORE_DELETE_FOLDER => 'onBeforeDeleteFolder',
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
            $documenttype = $this->documenttypeManager->findByMimetype($fileSource->getMimeType());
        } catch (\Exception $e) {
            $documenttype = $this->documenttypeManager->find('binary');
        }

        $file->setAssettype($documenttype->getType());
        $file->setDocumenttype($documenttype->getKey());

        try {
            $fileMetaSet = $this->metaSetManager->findOneByName('file');
            if ($fileMetaSet) {
                $file->addMetaSet($fileMetaSet);
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
                $folder->addMetaset($folderMetaSet);
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
