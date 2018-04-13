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
use PHPExiftool\Driver\Metadata\Metadata;
use PHPExiftool\Driver\Value\ValueInterface;
use PHPExiftool\Reader;

/**
 * GetId3 attribute reader.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExiftoolAttributeReader implements AttributeReaderInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
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
        return in_array($mediaType->getCategory(), array('image', 'video', 'audio'));
    }

    /**
     * {@inheritdoc}
     */
    public function read(PathSourceInterface $fileSource, MediaType $mediaType, AttributeBag $attributes)
    {
        $filename = $fileSource->getPath();

        $metadatas = $this->reader->reset()->files($filename)->first();
        foreach ($metadatas as $metadata) {
            /* @var $metadata Metadata */

            if (ValueInterface::TYPE_BINARY === $metadata->getValue()->getType()) {
                continue;
            }

            $groupName = strtolower($metadata->getTag()->getGroupName());
            $name = strtolower($metadata->getTag()->getName());
            $value = (string) $metadata->getValue();
            if ($groupName === 'system') {
                continue;
            }
            if (!ctype_print($value)) {
                continue;
            }

            $path = "exiftool.$groupName.$name";
            $attributes->set($path, $value);
        }
    }
}
