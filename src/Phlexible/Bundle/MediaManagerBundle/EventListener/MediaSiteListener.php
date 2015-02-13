<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\EventListener;

use Phlexible\Bundle\MediaManagerBundle\AttributeReader\AttributeBag;
use Phlexible\Bundle\MediaManagerBundle\AttributeReader\AttributeReaderInterface;
use Phlexible\Bundle\MediaManagerBundle\Volume\DeleteFileChecker;
use Phlexible\Bundle\MediaManagerBundle\Volume\DeleteFolderChecker;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
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
     * @var array
     */
    private $metasetMapping;

    /**
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param MetaSetManagerInterface   $metaSetManager
     * @param AttributeReaderInterface  $attributeReader
     * @param DeleteFileChecker         $deleteFileChecker
     * @param DeleteFolderChecker       $deleteFolderChecker
     * @param array                     $metasetMapping
     */
    public function __construct(
        MediaTypeManagerInterface $mediaTypeManager,
        MetaSetManagerInterface $metaSetManager,
        AttributeReaderInterface $attributeReader,
        DeleteFileChecker $deleteFileChecker,
        DeleteFolderChecker $deleteFolderChecker,
        array $metasetMapping)
    {
        $this->mediaTypeManager = $mediaTypeManager;
        $this->metaSetManager = $metaSetManager;
        $this->attributeReader = $attributeReader;
        $this->deleteFileChecker = $deleteFileChecker;
        $this->deleteFolderChecker = $deleteFolderChecker;
        $this->metasetMapping = $metasetMapping;
    }


    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            VolumeEvents::BEFORE_CREATE_FILE   => ['onBeforeCreateFile', 500],
            VolumeEvents::BEFORE_CREATE_FOLDER => ['onBeforeCreateFolder', 500],
            VolumeEvents::BEFORE_REPLACE_FILE  => ['onBeforeReplaceFile', 500],
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
            $mediaType = null;
        }

        if (!$mediaType) {
            $mediaType = $this->mediaTypeManager->find('binary');
        }

        $file->setMediaCategory($mediaType->getCategory());
        $file->setMediaType($mediaType->getName());

        foreach ($this->metasetMapping as $metasetName => $mapping) {
            if ($this->matches($mediaType, $mapping)) {
                $metaSet = $this->metaSetManager->findOneByName($metasetName);
                if ($metaSet) {
                    $file->addMetaSet($metaSet->getId());
                }
            }
        }

        $attributes = new AttributeBag($file->getAttributes());

        $mediaTypeName = $file->getMediaType();
        $mediaType = $this->mediaTypeManager->find($mediaTypeName);

        if ($this->attributeReader->supports($fileSource, $mediaType)) {
            $this->attributeReader->read($fileSource, $mediaType, $attributes);
        }

        $file->setAttributes($attributes->all());
    }

    /**
     * @param MediaType $mediaType
     * @param array     $mapping
     *
     * @return bool
     */
    private function matches(MediaType $mediaType, array $mapping)
    {
        if (empty($mapping)) {
            return true;
        }

        $match = false;
        if (!empty($mapping['name'])) {
            $match = $mediaType->getName() === $mapping['name'];
        }
        if (!empty($mapping['category'])) {
            $match = $mediaType->getCategory() === $mapping['category'];
        }

        return $match;
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
