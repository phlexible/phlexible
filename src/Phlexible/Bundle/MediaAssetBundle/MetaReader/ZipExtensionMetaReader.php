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
 * Zip extension meta reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ZipExtensionMetaReader implements MetaReaderInterface
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
    public function read(FileInterface $file, MetaBag $metaBag)
    {
        $filename = $file->getPhysicalPath();

        $metaData = new MetaData();
        $metaData->setTitle('ZIP');

        $zip = new \ZipArchive();
        $zip->open($filename);

        if (!empty($zip->comment)) {
            $metaData->set('comment', $zip->comment);
        }

        $zip->close();

        $metaBag->add($metaData);
    }
}