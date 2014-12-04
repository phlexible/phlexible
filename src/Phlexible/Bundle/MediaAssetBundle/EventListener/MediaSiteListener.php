<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\EventListener;

use Phlexible\Bundle\MediaAssetBundle\AttributeReader\AttributeReaderInterface;
use Phlexible\Bundle\MediaAssetBundle\Model\AttributeBag;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Phlexible\Component\Volume\Event\CreateFileEvent;
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
     * @var AttributeReaderInterface
     */
    private $attributeReader;

    /**
     * @var MediaTypeManagerInterface
     */
    private $mediaTypeManager;

    /**
     * @param AttributeReaderInterface  $attributeReader
     * @param MediaTypeManagerInterface $mediaTypeManager
     */
    public function __construct(AttributeReaderInterface $attributeReader, MediaTypeManagerInterface $mediaTypeManager)
    {
        $this->attributeReader = $attributeReader;
        $this->mediaTypeManager = $mediaTypeManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            VolumeEvents::BEFORE_CREATE_FILE => 'onBeforeCreateFile',
            VolumeEvents::BEFORE_REPLACE_FILE => 'onBeforeReplaceFile',
        ];
    }

    /**
     * @param CreateFileEvent $event
     */
    public function onBeforeCreateFile(CreateFileEvent $event)
    {
        $file = $event->getFile();
        $fileSource = $event->getFileSource();

        $this->processAttributes($file, $fileSource);
    }

    /**
     * @param ReplaceFileEvent $event
     */
    public function onBeforeReplaceFile(ReplaceFileEvent $event)
    {
        $file = $event->getFile();
        $fileSource = $event->getFileSource();

        $this->processAttributes($file, $fileSource);
    }

    /**
     * @param ExtendedFileInterface $file
     * @param PathSourceInterface   $fileSource
     */
    private function processAttributes(ExtendedFileInterface $file, PathSourceInterface $fileSource)
    {
        $attributes = new AttributeBag($file->getAttributes());

        $mediaTypeName = $file->getMediaType();
        $mediaType = $this->mediaTypeManager->find($mediaTypeName);

        if ($this->attributeReader->supports($fileSource, $mediaType)) {
            $this->attributeReader->read($fileSource, $mediaType, $attributes);
        }

        $file->setAttributes($attributes->all());
    }
}
