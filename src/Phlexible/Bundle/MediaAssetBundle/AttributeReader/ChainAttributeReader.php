<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Phlexible\Bundle\MediaAssetBundle\AttributesBag;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;

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
    public function supports(FileInterface $file, PathSourceInterface $fileSource)
    {
        foreach ($this->readers as $reader) {
            if ($reader->isAvailable() && $reader->supports($file, $fileSource)) {
                return true;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read(FileInterface $file, PathSourceInterface $fileSource, AttributesBag $attributes)
    {
        foreach ($this->readers as $reader) {
            if ($reader->isAvailable() && $reader->supports($file, $fileSource)) {
                $reader->read($file, $fileSource, $attributes);
            }
        }
    }
}