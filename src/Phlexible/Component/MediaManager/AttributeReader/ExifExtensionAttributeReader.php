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
 * Exif extension attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExifExtensionAttributeReader implements AttributeReaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return extension_loaded('exif') && function_exists('exif_read_data');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(PathSourceInterface $fileSource, MediaType $mediaType)
    {
        return $mediaType->getCategory() === 'image'
            && \exif_imagetype($fileSource->getPath()) === IMAGETYPE_JPEG;
    }

    /**
     * {@inheritdoc}
     */
    public function read(PathSourceInterface $fileSource, MediaType $mediaType, AttributeBag $attributes)
    {
        $filename = $fileSource->getPath();

        $result = \exif_read_data($filename, '', true);

        if (!empty($result['IFD0'])) {
            foreach ($result['IFD0'] as $key => $value) {
                $attributes->set("exif.$key", $value);
            }
        }
    }
}
