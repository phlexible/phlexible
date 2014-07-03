<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Phlexible\Bundle\MediaAssetBundle\AttributeMetaData;
use Phlexible\Bundle\MediaAssetBundle\MetaBag;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Zend PDF attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ZendPdfAttributeReader implements AttributeReaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
       return class_exists('Zend_Pdf');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FileInterface $file)
    {
        return strtolower($file->getAttribute('documenttype')) === 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function read(FileInterface $file, MetaBag $metaBag)
    {
        $filename = $file->getPhysicalPath();

        $metaData = new AttributeMetaData();
        $metaData->setTitle('PDF attributes');

        try {
            $pdf = \Zend_Pdf::load($filename);

            $metaData->set('pages', count($pdf->pages));

            $metaBag->add($metaData);
        } catch (\Exception $e) {
        }
    }

}