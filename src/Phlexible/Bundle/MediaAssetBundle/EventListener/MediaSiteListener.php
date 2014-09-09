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
use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\MediaSiteEvents;
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;
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
     * @param BeforeCreateFileEvent $event
     */
    public function onBeforeCreateFile(BeforeCreateFileEvent $event)
    {
        $fileSource = $event->getAction()->getFileSource();
        $fileAttributes = $event->getAction()->getAttributes();

        $this->process($fileSource, $fileAttributes);
    }

    /**
     * @param BeforeReplaceFileEvent $event
     */
    public function onBeforeReplaceFile(BeforeReplaceFileEvent $event)
    {
        $fileSource = $event->getAction()->getFileSource();
        $fileAttributes = $event->getAction()->getAttributes();

        $this->process($fileSource, $fileAttributes);
    }

    /**
     * @param PathSourceInterface $fileSource
     * @param AttributeBag        $fileAttributes
     */
    private function process(PathSourceInterface $fileSource, AttributeBag $fileAttributes)
    {
        $attributes = new AttributeBag();

        $documenttype = $fileAttributes->get('documenttype', '');
        $assettype = $fileAttributes->get('assettype', '');

        if ($this->attributeReader->supports($fileSource, $documenttype, $assettype)) {
            $this->attributeReader->read($fileSource, $documenttype, $assettype, $attributes);
        }

        $fileAttributes->set('attributes', $attributes->all());
    }
}
