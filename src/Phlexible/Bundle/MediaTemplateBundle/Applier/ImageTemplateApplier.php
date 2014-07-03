<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Applier;

use Brainbits\ImageConverter\Driver\DriverInterface;
use Brainbits\ImageConverter\Image\Point;
use Brainbits\ImageConverter\ImageConverter;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaTemplateBundle\Model\ImageTemplate;
use Psr\Log\LoggerInterface;

/**
 * Image cache worker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImageTemplateApplier
{
    /**
     * @var ImageConverter
     */
    private $converter;

    /**
     * @param ImageConverter $converter
     */
    public function __construct(ImageConverter $converter)
    {
        $this->converter = $converter;
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
        return $this->converter->getDriver()->getConvertDriver()->getProcessRunner()->getLogger();
    }

    /**
     * @param ImageTemplate $template
     *
     * @return string
     */
    public function getExtension(ImageTemplate $template)
    {
        if ($template->getParameter('format')) {
            return $template->getParameter('format');
        } else {
            return 'jpg';
        }
    }

    /**
     * @param ImageTemplate $template
     *
     * @return string
     */
    public function getMimetype(ImageTemplate $template)
    {
        if ($template->getParameter('format')) {
            return 'image/' . $template->getParameter('format');
        } else {
            return 'image/jpg';
        }
    }

    /**
     * @param ImageTemplate $template
     * @param FileInterface $file
     * @param string        $filename
     * @param string        $outFilename
     *
     * @return DriverInterface
     */
    public function apply(ImageTemplate $template, FileInterface $file, $filename, $outFilename)
    {
        $image = $this->converter->load($filename, 0);

        if ($focalpoint = $file->getAttribute('focalpoint')) {
            $image->setCenter(new Point($focalpoint['x'], $focalpoint['y']));
        }

        if ($template->hasParameter('format', true)) {
            $image->setFormat($template->getParameter('format'));
        }

        if ($template->hasParameter('quality', true)) {
            $image->setQuality($template->getParameter('quality'));
        }

        if ($template->hasParameter('for_web', true) && $template->getParameter('for_web')) {
            $image->stripProfiles();

            if ($template->hasParameter('colorspace')) {
                $colorspace = $template->getParameter('colorspace');
                if ($colorspace == 'cmyk') {
                    $colorspace = 'rgb';
                }
                $image->setColorspace($colorspace);
            }
        } elseif ($template->hasParameter('colorspace', true)) {
            $image->setColorspace($template->getParameter('colorspace'));
        }

        if ($template->hasParameter('depth', true)) {
            $image->setColorDepth($template->getParameter('depth'));
        }

        if ($template->hasParameter('tiffcompression', true)) {
            $image->setTiffCompression($template->getParameter('tiffcompression'));
        }

        $backgroundColor = null;
        if ($template->hasParameter('backgroundcolor', true)) {
            $backgroundColor = $template->getParameter('backgroundcolor');
        }

        /*
        $scale = ImageToolkit::SCALE_NONE;
        if ($template->hasParameter('scale', true))
        {
            $scale = $template->getParameter('scale');
        }
        */

        $method = $template->getParameter('method');

        $image->resizeMethod(
            $method,
            $template->getParameter('width'),
            $template->getParameter('height'),
            //$scale,
            $backgroundColor
        );

        $image->write($outFilename);

        return $this->converter->load($outFilename, 0);
    }
}
