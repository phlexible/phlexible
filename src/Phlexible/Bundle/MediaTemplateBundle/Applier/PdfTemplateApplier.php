<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Applier;

use Brainbits\SwfTools\Pdf2swf;
use Phlexible\Bundle\MediaTemplateBundle\Model\PdfTemplate;
use Psr\Log\LoggerInterface;

/**
 * PDF template applier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PdfTemplateApplier
{
    /**
     * @var Pdf2Swf
     */
    private $pdf2swf;

    /**
     * @param Pdf2Swf $pdf2swf
     */
    public function __construct(Pdf2Swf $pdf2swf)
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
        $options = $this->pdf2swf->options();

        $options->setParam('storeallcharacters');

        if ($template->hasParameter('pages')) {
            $options->setPages($template->getParameter('pages'));
        }

        if ($template->hasParameter('password')) {
            $options->setPassword($template->getParameter('password'));
        }

        if ($template->hasParameter('zlib')) {
            $options->setZlib($template->getParameter('zlib'));
        }

        if ($template->hasParameter('ignore')) {
            $options->setIgnore($template->getParameter('ignore'));
        }

        if ($template->hasParameter('quality')) {
            $options->setJpegQuality($template->getParameter('quality'));
        }

        if ($template->hasParameter('same_window')) {
            $options->setSameWindow($template->getParameter('same_window'));
        }

        if ($template->hasParameter('stop')) {
            $options->setStop($template->getParameter('stop'));
        } else {
            $options->setStop(true);
        }

        if ($template->hasParameter('flash_version')) {
            $options->setFlashVersion($template->getParameter('flash_version'));
        } else {
            $options->setFlashVersion(9);
        }

        if ($template->hasParameter('font_dir')) {
            $options->setFontDir($template->getParameter('font_dir'));
        }

        if ($template->hasParameter('default_viewer')) {
            $options->setDefaultViewer($template->getParameter('default_viewer'));
        }

        if ($template->hasParameter('default_loader')) {
            $options->setDefaultLoader($template->getParameter('default_loader'));
        }

        if ($template->hasParameter('viewer')) {
            $options->setViewer($template->getParameter('viewer'));
        }

        if ($template->hasParameter('preloader')) {
            $options->setPreLoader($template->getParameter('preloader'));
        }

        if ($template->hasParameter('shapes')) {
            $options->setShapes($template->getParameter('shapes'));
        }

        if ($template->hasParameter('fonts')) {
            $options->setFonts($template->getParameter('fonts'));
        } else {
            $options->setFonts(true);
        }

        if ($template->hasParameter('flatten')) {
            $options->setFlatten($template->getParameter('flatten'));
        } else {
            $options->setFlatten(true);
        }

        if ($template->hasParameter('max_time')) {
            $options->setMaxTime($template->getParameter('max_time'));
        }

        $options->setOutput($outFilename);

        $this->pdf2swf->write($inFilename, $options->getOptions());
    }
}
