<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\EventListener;

use Phlexible\Bundle\MediaManagerBundle\MetaSet\MetaSetMapper;
use Phlexible\Component\MediaManager\AttributeReader\AttributeBag;
use Phlexible\Component\MediaManager\AttributeReader\AttributeReaderInterface;
use Phlexible\Component\MediaManager\Volume\DeleteFileChecker;
use Phlexible\Component\MediaManager\Volume\DeleteFolderChecker;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;
use Phlexible\Component\Volume\Event\CreateFileEvent;
use Phlexible\Component\Volume\Event\FileEvent;
use Phlexible\Component\Volume\Event\FolderEvent;
use Phlexible\Component\Volume\Event\ReplaceFileEvent;
use Phlexible\Component\Volume\FileSource\PathSourceInterface;
use Phlexible\Component\Volume\VolumeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Media site listener.
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
     * @var AttributeReaderInterface
     */
    private $attributeReader;

    /**
     * @var DeleteFileChecker
     */
    private $deleteFileChecker;

    /**
     * @var DeleteFolderChecker
     */
    private $deleteFolderChecker;

    /**
     * @var MetaSetMapper
     */
    private $metaSetMapper;

    /**
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param MetaSetManagerInterface   $metaSetManager
     * @param AttributeReaderInterface  $attributeReader
     * @param DeleteFileChecker         $deleteFileChecker
     * @param DeleteFolderChecker       $deleteFolderChecker
     * @param MetaSetMapper             $metaSetMapper
     */
    public function __construct(
        MediaTypeManagerInterface $mediaTypeManager,
        MetaSetManagerInterface $metaSetManager,
        AttributeReaderInterface $attributeReader,
        DeleteFileChecker $deleteFileChecker,
        DeleteFolderChecker $deleteFolderChecker,
        MetaSetMapper $metaSetMapper
    ) {
        $this->mediaTypeManager = $mediaTypeManager;
        $this->metaSetManager = $metaSetManager;
        $this->attributeReader = $attributeReader;
        $this->deleteFileChecker = $deleteFileChecker;
        $this->deleteFolderChecker = $deleteFolderChecker;
        $this->metaSetMapper = $metaSetMapper;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            VolumeEvents::BEFORE_CREATE_FILE => ['onBeforeCreateFile', 500],
            VolumeEvents::BEFORE_CREATE_FOLDER => ['onBeforeCreateFolder', 500],
            VolumeEvents::BEFORE_REPLACE_FILE => ['onBeforeReplaceFile', 500],
            VolumeEvents::BEFORE_DELETE_FILE => 'onBeforeDeleteFile',
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
            $mediaType = null;
        }

        if (!$mediaType) {
            $mediaType = $this->mediaTypeManager->find('binary');
        }

        $file->setMediaCategory($mediaType->getCategory());
        $file->setMediaType($mediaType->getName());

        $this->metaSetMapper->map($file, $mediaType);

        $attributes = new AttributeBag($file->getAttributes());

        $mediaTypeName = $file->getMediaType();
        $mediaType = $this->mediaTypeManager->find($mediaTypeName);

        if ($this->attributeReader->supports($fileSource, $mediaType)) {
            $this->attributeReader->read($fileSource, $mediaType, $attributes);
        }

        $file->setAttribute('fileattributes', $attributes->all());
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
