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
 * Zend PDF meta reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ZendPdfMetaReader implements MetaReaderInterface
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

        $metaData = new MetaData();
        $metaData->setTitle('PDF');

        try {
            $pdf = \Zend_Pdf::load($filename);

            if (!empty($pdf->properties['CreationDate'])) {
                $metaData->set('createDate', $pdf->properties['CreationDate']);
            }

            if (!empty($pdf->properties['ModDate'])) {
                $metaData->set('modifyDate', $pdf->properties['ModDate']);
            }

            if (!empty($pdf->properties['Title'])) {
                $metaData->set('title', $pdf->properties['Title']);
            }

            if (!empty($pdf->properties['Creator'])) {
                $metaData->set('creator', $pdf->properties['Creator']);
            }

            if (!empty($pdf->properties['Producer'])) {
                $metaData->set('producer', $pdf->properties['Producer']);
            }

            if (!empty($pdf->properties['Author'])) {
                $metaData->set('author', $pdf->properties['Author']);
            }

            $metaBag->add($metaData);
        } catch (\Exception $e) {

        }
    }

}