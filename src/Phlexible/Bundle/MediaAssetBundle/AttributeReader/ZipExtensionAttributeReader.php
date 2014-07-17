<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Phlexible\Bundle\MediaAssetBundle\AttributeMetaData;
use Phlexible\Bundle\MediaAssetBundle\Attributes;
use Phlexible\Bundle\MediaAssetBundle\AttributesBag;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Zip extension attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ZipExtensionAttributeReader implements AttributeReaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
       return extension_loaded('zip');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FileInterface $file)
    {
        return strtolower($file->getAttribute('documenttype')) === 'zip';
    }

    /**
     * {@inheritdoc}
     */
    public function read(FileInterface $file, AttributesBag $attributes)
    {
        $filename = $file->getPhysicalPath();

        $zip = new \ZipArchive();
        $zip->open($filename);

        if (!empty($zip->numFiles)) {
            $attributes
                ->set('zip.num_files', $zip->numFiles)
                ->set('zip.comment', $zip->comment);
        }

        $zip->close();
    }
}