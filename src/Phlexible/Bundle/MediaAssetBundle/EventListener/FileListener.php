<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\EventListener;

use Phlexible\Bundle\MediaAssetBundle\AttributeReader\AttributeReaderInterface;
use Phlexible\Bundle\MediaAssetBundle\AttributesBag;
use Phlexible\Bundle\MediaSiteBundle\Event\BeforeCreateFileEvent;

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
        $file = $event->getAction()->getFile();
        $fileSource = $event->getAction()->getFileSource();

        $attributes = new AttributesBag();

        $this->attributeReader->read($file, $fileSource, $attributes);

        $file
            ->setAttribute('attributes', $attributes->all());
    }
}
