<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Brainbits\PdfToText\PdfInfo;
use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;

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
    public function supports(PathSourceInterface $fileSource, $documenttype, $assettype)
    {
        return $documenttype === 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function read(PathSourceInterface $fileSource, $documenttype, $assettype, AttributeBag $attributes)
    {
        $filename = $fileSource->getPath();

        try {
            $infos = $this->pdfinfo->getInfo($filename);

            foreach ($infos as $key => $value) {
                $attributes->set(strtolower("pdf.$key"), $value);
            }
        } catch (\Exception $e) {
        }
    }
}