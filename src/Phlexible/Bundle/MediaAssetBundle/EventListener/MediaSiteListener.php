<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\EventListener;

use Phlexible\Bundle\MediaAssetBundle\AttributeReader\AttributeReaderInterface;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeCreateFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeReplaceFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\CreateFileEvent;
use Phlexible\Bundle\MediaSiteBundle\Event\ReplaceFileEvent;
use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\MediaSiteEvents;
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
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
     * @param AttributeReaderInterface $attributeReader
     */
    public function __construct(AttributeReaderInterface $attributeReader)
    {
        $this->attributeReader = $attributeReader;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            MediaSiteEvents::BEFORE_CREATE_FILE => 'onBeforeCreateFile',
            MediaSiteEvents::BEFORE_REPLACE_FILE => 'onBeforeReplaceFile',
        );
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
     * @param FileInterface       $file
     * @param PathSourceInterface $fileSource
     */
    private function processAttributes(FileInterface $file, PathSourceInterface $fileSource)
    {
        $attributes = new AttributeBag();

        $assettype = $file->getAssettype();
        $documenttype = $file->getDocumenttype();

        if ($this->attributeReader->supports($fileSource, $documenttype, $assettype)) {
            $this->attributeReader->read($fileSource, $documenttype, $assettype, $attributes);
        }

        $file->getAttributes()->set('attributes', $attributes->all());
    }
}
