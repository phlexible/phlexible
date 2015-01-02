<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ContentExtractor;

use Phlexible\Bundle\MediaExtractorBundle\Extractor\ExtractorInterface;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;
use Poppler\Processor\PdfFile;

/**
 * PDF to text content extractor
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class PdfToTextExtractor implements ExtractorInterface
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
    public function supports(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        return $targetFormat === 'text' && $mediaType->getName() === 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        $output = $this->pdfFile->toText($file->getPhysicalPath());

        // set pdftotext output as content
        // strip UTF-8 control characters
        $content = new Content(preg_replace('/\p{Co}/u', '', $output));

        return $content;
    }

}
