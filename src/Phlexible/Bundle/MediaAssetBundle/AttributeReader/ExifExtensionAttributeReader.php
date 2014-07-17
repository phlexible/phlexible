<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Phlexible\Bundle\MediaAssetBundle\AttributesBag;
use Phlexible\Bundle\MediaAssetBundle\Attributes;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

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
    public function supports(FileInterface $file)
    {
        if (!file_exists($file->getPhysicalPath())) {
            return false;
        }

        return strtolower($file->getAttribute('assettype') === 'image')
            && \exif_imagetype($file->getPhysicalPath()) === IMAGETYPE_JPEG;
    }

    /**
     * {@inheritdoc}
     */
    public function read(FileInterface $file, AttributesBag $attributes)
    {
        $filename = $file->getPhysicalPath();

        $result = \exif_read_data($filename, '', true);

        if (!empty($result['IFD0'])) {
            foreach ($result['IFD0'] as $key => $value) {
                $attributes->set("exif.$key", $value);
            }
        }
    }

}