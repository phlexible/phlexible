<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\AttributeReader;

use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\Volume\FileSource\PathSourceInterface;

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
    public function supports(PathSourceInterface $fileSource, MediaType $mediaType)
    {
        foreach ($this->readers as $reader) {
            if ($reader->isAvailable() && $reader->supports($fileSource, $mediaType)) {
                return true;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read(PathSourceInterface $fileSource, MediaType $mediaType, AttributeBag $attributes)
    {
        foreach ($this->readers as $reader) {
            if ($reader->isAvailable() && $reader->supports($fileSource, $mediaType)) {
                $reader->read($fileSource, $mediaType, $attributes);
            }
        }
    }
}
