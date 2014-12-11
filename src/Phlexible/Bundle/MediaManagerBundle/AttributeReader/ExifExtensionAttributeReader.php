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
