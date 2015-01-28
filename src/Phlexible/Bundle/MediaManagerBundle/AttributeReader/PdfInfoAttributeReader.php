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
use Poppler\Processor\PdfFile;

/**
 * pdfinfo attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PdfInfoAttributeReader implements AttributeReaderInterface
{
    /**
     * @var PdfFile
     */
    private $pdfFile;

    /**
     * @param PdfFile $pdfFile
     */
    public function __construct(PdfFile $pdfFile)
    {
        $this->pdfFile = $pdfFile;
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
    public function supports(PathSourceInterface $fileSource, MediaType $mediaType)
    {
        return $mediaType->getName() === 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function read(PathSourceInterface $fileSource, MediaType $mediaType, AttributeBag $attributes)
    {
        $filename = $fileSource->getPath();

        try {
            $infos = $this->pdfFile->getInfo($filename);

            foreach ($infos as $key => $value) {
                $attributes->set(strtolower("pdfinfo.$key"), $value);
            }
        } catch (\Exception $e) {
        }
    }
}
