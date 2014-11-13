<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ContentExtractor;

use Brainbits\PdfToText\PdfToText;
use Brainbits\PdfToText\PdfToTextOptions;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;

/**
 * PDF to text content extractor
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class PdfToTextExtractor implements ContentExtractorInterface
{
    /**
     * @var PdfToText
     */
    private $pdfToText;

    /**
     * @param PdfToText $pdfToText
     */
    public function __construct(PdfToText $pdfToText)
    {
        $this->pdfToText = $pdfToText;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return $this->pdfToText->isAvailable();
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
        $pdfToTextOptions = new PdfToTextOptions();
        $pdfToTextOptions->encoding('UTF-8');
        $output = $this->pdfToText->write($file->getPhysicalPath(), $pdfToTextOptions->getOptions());

        // set pdftotext output as content
        // strip UTF-8 control characters
        $content = new Content(preg_replace('/\p{Co}/u', '', $output));

        return $content;
    }

}
