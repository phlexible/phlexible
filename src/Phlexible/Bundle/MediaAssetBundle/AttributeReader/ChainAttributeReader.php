<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;

/**
 * Chain attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ChainAttributeReader implements AttributeReaderInterface
{
    /**
     * @var AttributeReaderInterface[]
     */
    private $readers;

    /**
     * @param AttributeReaderInterface[] $readers
     */
    public function __construct(array $readers)
    {
        $this->readers = $readers;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(PathSourceInterface $fileSource, $documenttype, $assettype)
    {
        foreach ($this->readers as $reader) {
            if ($reader->isAvailable() && $reader->supports($fileSource, $documenttype, $assettype)) {
                return true;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read(PathSourceInterface $fileSource, $documenttype, $assettype, AttributeBag $attributes)
    {
        foreach ($this->readers as $reader) {
            if ($reader->isAvailable() && $reader->supports($fileSource, $documenttype, $assettype)) {
                $reader->read($fileSource, $documenttype, $assettype, $attributes);
            }
        }
    }
}