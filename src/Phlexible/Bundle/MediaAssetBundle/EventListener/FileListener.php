<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\EventListener;

use Phlexible\Bundle\MediaAssetBundle\AttributeReader\AttributeReaderInterface;
use Phlexible\Bundle\MediaAssetBundle\MetaBag;
use Phlexible\Bundle\MediaAssetBundle\MetaReader\MetaReaderInterface;
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
     * @var MetaReaderInterface
     */
    private $metaReader;

    /**
     * @param AttributeReaderInterface $attributeReader
     * @param MetaReaderInterface      $metaReader
     */
    public function __construct(AttributeReaderInterface $attributeReader, MetaReaderInterface $metaReader)
    {
        $this->attributeReader = $attributeReader;
        $this->metaReader = $metaReader;
    }

    /**
     * @param BeforeCreateFileEvent $event
     */
    public function onBeforeCreateFile(BeforeCreateFileEvent $event)
    {
        $file = $event->getAction()->getFile();

        $metaBag = new MetaBag();

        $this->attributeReader->read($file, $metaBag);
        $this->metaReader->read($file, $metaBag);

        $file
            ->setAttribute('assetmeta', $metaBag);
    }
}
