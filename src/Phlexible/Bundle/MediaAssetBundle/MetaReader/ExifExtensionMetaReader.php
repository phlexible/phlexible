<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\MetaReader;

use Phlexible\Bundle\MediaAssetBundle\MetaBag;
use Phlexible\Bundle\MediaAssetBundle\MetaData;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Exif extension meta reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ExifExtensionMetaReader implements MetaReaderInterface
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

        return strtolower($file->getAttribute('assettype') === 'image') &&
        \exif_imagetype($file->getPhysicalPath()) === IMAGETYPE_JPEG;
    }

    /**
     * {@inheritdoc}
     */
    public function read(FileInterface $file, MetaBag $metaBag)
    {
        $filename = $file->getPhysicalPath();

        $metaData = new MetaData();
        $metaData->setTitle('EXIF');

        $result = \exif_read_data($filename, '', true);

        if (!empty($result['IFD0'])) {
            foreach ($result['IFD0'] as $key => $value) {
                $metaData->set($key, $value);
            }
        }

        $metaBag->add($metaData);
    }

}