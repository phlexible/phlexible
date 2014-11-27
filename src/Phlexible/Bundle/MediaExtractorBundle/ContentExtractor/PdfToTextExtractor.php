<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ContentExtractor;

use Brainbits\PdfToText\Processor\PdfFile;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;

/**
 * PDF to text content extractor
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class PdfToTextExtractor implements ContentExtractorInterface
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
    public function supports(FileInterface $file)
    {
        return strtolower($file->getDocumenttype()) === 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(FileInterface $file)
    {
        $output = $this->pdfFile->toText($file->getPhysicalPath());

        // set pdftotext output as content
        // strip UTF-8 control characters
        $content = new Content(preg_replace('/\p{Co}/u', '', $output));

        return $content;
    }

}
