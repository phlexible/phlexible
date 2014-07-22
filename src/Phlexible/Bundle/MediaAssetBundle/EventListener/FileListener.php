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
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;

/**
 * File listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileListener
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
     * @param BeforeCreateFileEvent $event
     */
    public function onBeforeCreateFile(BeforeCreateFileEvent $event)
    {
        $fileSource = $event->getAction()->getFileSource();
        $fileAttributes = $event->getAction()->getAttributes();

        $attributes = new AttributeBag();

        $documenttype = $fileAttributes->get('documenttype', '');
        $assettype = $fileAttributes->get('assettype', '');

        if ($this->attributeReader->supports($fileSource, $documenttype, $assettype)) {
            $this->attributeReader->read($fileSource, $documenttype, $assettype, $attributes);
        }

        $fileAttributes->set('attributes', $attributes->all());
    }
}
