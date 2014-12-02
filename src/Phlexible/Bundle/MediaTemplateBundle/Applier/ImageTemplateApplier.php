<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Applier;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\CMYK;
use Imagine\Image\Palette\Grayscale;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Image\Point\Center;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
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
     * @var ImagineInterface
     */
    private $imagine;

    /**
     * @param ImagineInterface $imagine
     */
    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
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
        return null;
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
     * @return ImageInterface
     */
    public function apply(ImageTemplate $template, FileInterface $file, $filename, $outFilename)
    {
        $image = $this->imagine->open($filename);

        $options = array();

        if ($template->hasParameter('format', true)) {
            $options['format'] = $template->getParameter('format');
        }

        if ($template->hasParameter('quality', true)) {
            if (!empty($options['format']) && $options['format'] === 'png') {
                $options['png_compression_level'] = floor($template->getParameter('quality') / 10);
                $options['png_compression_filter'] = $template->getParameter('quality') % 10;
            } elseif (!empty($options['format']) && $options['format'] === 'jpg') {
                $options['jpeg_quality'] = $template->getParameter('quality');
            }
        }

        if ($template->hasParameter('tiffcompression', true)) {
            //$image->setTiffCompression($template->getParameter('tiffcompression'));
        }

        if ($template->hasParameter('for_web', true) && $template->getParameter('for_web')) {
            $image->strip();

            if ($template->hasParameter('colorspace')) {
                $colorspace = $template->getParameter('colorspace');
                if ($colorspace === 'grayscale') {
                    $image->usePalette(new Grayscale());
                } else {
                    $image->usePalette(new RGB());
                }
            }
        } elseif ($template->hasParameter('colorspace', true)) {
            $colorspace = $template->getParameter('colorspace');
            if ($colorspace === 'grayscale') {
                $image->usePalette(new Grayscale());
            } elseif ($colorspace === 'cmyk') {
                $image->usePalette(new CMYK());
            } else {
                $image->usePalette(new RGB());
            }
        }

        if ($template->hasParameter('scale', true)) {
            if ($template->getParameter('scale') === 'up') {
                // only scale up
            } elseif ($template->getParameter('scale') === 'down') {
                // only scale down
            }
        }

        $method = $template->getParameter('method');

        if ($method === 'width') {
            $size = $image->getSize()->widen($template->getParameter('width'));
            $image->resize($size);
        } elseif ($method === 'height') {
            $size = $image->getSize()->heighten($template->getParameter('height'));
            $image->resize($size);
        } elseif ($method === 'exact') {
            $size = new Box($template->getParameter('width'), $template->getParameter('height'));
            $image->resize($size);
        } elseif ($method === 'fit') {
            $size = new Box($template->getParameter('width'), $template->getParameter('height'));
            $image = $image->thumbnail($size, ImageInterface::THUMBNAIL_INSET);
        } elseif ($method === 'exactFit') {
            $size = new Box($template->getParameter('width'), $template->getParameter('height'));
            $layer = $image->thumbnail($size, ImageInterface::THUMBNAIL_INSET);
            $layerSize = $layer->getSize();

            $palette = new RGB();
            if ($template->hasParameter('backgroundcolor', true)) {
                $color = $palette->color($template->getParameter('backgroundcolor'), 100);
            } else {
                $color = $palette->color('#fff', 0);
            }
            $image = $this->imagine->create($size, $color);
            $image->paste($layer, new Point(
                floor(($size->getWidth() - $layerSize->getWidth()) / 2),
                floor(($size->getHeight() - $layerSize->getHeight()) / 2)
            ));
        } elseif ($method === 'crop') {
            $size = new Box($template->getParameter('width'), $template->getParameter('height'));
            $imageSize = $image->getSize();

            if (!$size->contains($imageSize)) {
                $ratios = array(
                    $size->getWidth() / $imageSize->getWidth(),
                    $size->getHeight() / $imageSize->getHeight()
                );
                $ratio = max($ratios);
                if (!$imageSize->contains($size)) {
                    $imageSize = new Box(
                        min($imageSize->getWidth(), $size->getWidth()),
                        min($imageSize->getHeight(), $size->getHeight())
                    );
                } else {
                    $imageSize = $imageSize->scale($ratio);
                    $image->resize($imageSize);
                }

                if ($focalpoint = $file->getAttribute('focalpoint') && !empty($focalpoint['active'])) {
                    // TODO: correct?
                    $point = new Point($focalpoint['x'], $focalpoint['y']);
                } else {
                    $point = new Point(
                        max(0, round(($imageSize->getWidth() - $size->getWidth()) / 2)),
                        max(0, round(($imageSize->getHeight() - $size->getHeight()) / 2))
                    );
                }

                $image->crop($point, $size);
            }
        }

        return $image->save($outFilename, $options);
    }
}
