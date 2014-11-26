<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Applier;

use Phlexible\Bundle\MediaTemplateBundle\Model\PdfTemplate;
use Psr\Log\LoggerInterface;
use SwfTools\Binary\Pdf2swf;

/**
 * PDF template applier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PdfTemplateApplier
{
    /**
     * @var Pdf2swf
     */
    private $pdf2swf;

    /**
     * @param Pdf2swf $pdf2swf
     */
    public function __construct(Pdf2swf $pdf2swf)
    {
        $this->pdf2swf = $pdf2swf;
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    public function isAvailable($filename)
    {
        return true;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->pdf2swf->getProcessRunner()->getLogger();
    }

    /**
     * @param PdfTemplate $template
     *
     * @return string
     */
    public function getExtension(PdfTemplate $template)
    {
        return 'swf';
    }

    /**
     * @param PdfTemplate $template
     *
     * @return string
     */
    public function getMimetype(PdfTemplate $template)
    {
        return 'application/x-shockwave-flash';
    }

    /**
     * @param PdfTemplate $template
     * @param string      $inFilename
     * @param string      $outFilename
     */
    public function apply(PdfTemplate $template, $inFilename, $outFilename)
    {
        $options = array();

        $pageRange = '1-';
        if ($template->getParameter('pages')) {
            $pageRange = $template->getParameter('pages');
        }

        if ($template->getParameter('zlib_enable')) {
            $options[] = Pdf2swf::OPTION_ZLIB_ENABLE;
        }

        if ($template->getParameter('simpleviewer_enable')) {
            $options[] = Pdf2swf::OPTION_ENABLE_SIMPLEVIEWER;
        }

        $jpegQuality = 75;
        if ($template->getParameter('jpeg_quality')) {
            $jpegQuality = $template->getParameter('jpeg_quality');
        }

        if ($template->getParameter('links_new_window')) {
            $options[] = Pdf2swf::OPTION_LINKS_OPENNEWWINDOW;
        }

        if ($template->getParameter('links_disable')) {
            $options[] = Pdf2swf::OPTION_LINKS_DISABLE;
        }

        $resolution = 72;
        if ($template->getParameter('resolution')) {
            $resolution = $template->getParameter('resolution');
        }

        $frameRate = 15;
        if ($template->getParameter('framerate')) {
            $frameRate = $template->getParameter('framerate');
        }

        $this->pdf2swf->toSwf($inFilename, $outFilename, $options, Pdf2swf::CONVERT_POLY2BITMAP, $resolution, $pageRange, $frameRate, $jpegQuality);
    }
}
