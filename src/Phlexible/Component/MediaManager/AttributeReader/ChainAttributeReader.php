<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\AttributeReader;

use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\Volume\FileSource\PathSourceInterface;

/**
 * Chain attribute reader.
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
