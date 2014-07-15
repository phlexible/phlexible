<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Brainbits\PdfToText\PdfInfo;
use Phlexible\Bundle\MediaAssetBundle\AttributeMetaData;
use Phlexible\Bundle\MediaAssetBundle\MetaBag;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * pdfinfo attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PdfInfoAttributeReader implements AttributeReaderInterface
{
    /**
     * @var PdfInfo
     */
    private $pdfinfo;

    /**
     * @param PdfInfo $pdfinfo
     */
    public function __construct(PdfInfo $pdfinfo)
    {
        $this->pdfinfo = $pdfinfo;
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
            $infos = $this->pdfinfo->getInfo($filename);

            foreach ($infos as $key => $value) {
                $metaData->set(strtolower($key), $value);
            }

            $metaBag->add($metaData);
        } catch (\Exception $e) {
        }
    }
}