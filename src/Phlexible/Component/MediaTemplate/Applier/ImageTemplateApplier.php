<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\Applier;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\CMYK;
use Imagine\Image\Palette\Grayscale;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\ImageTemplate;
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
     * @param ImageTemplate         $template
     * @param ExtendedFileInterface $file
     * @param string                $filename
     * @param string                $outFilename
     *
     * @return ImageInterface
     */
    public function apply(ImageTemplate $template, ExtendedFileInterface $file, $filename, $outFilename)
    {
        $image = $this->imagine->open($filename);
        if ($image->layers()->count() > 1) {
            // workaround for multi-page pdfs
            // might be removed when https://github.com/avalanche123/Imagine/pull/451 is merged
            $tmpFilename = sys_get_temp_dir().'/'.basename($filename);
            $image->layers()->get(0)->save($tmpFilename.".jpg");
            $image = $this->imagine->open($tmpFilename.".jpg");
        }

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
                if ($colorspace === 'grayscale' || $colorspace === 'gray') {
                    $image->usePalette(new Grayscale());
                } else {
                    $image->usePalette(new RGB());
                }
            }
        } elseif ($template->hasParameter('colorspace', true)) {
            $colorspace = $template->getParameter('colorspace');
            if ($colorspace === 'grayscale' || $colorspace === 'gray') {
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
            $templateSize = $image->getSize()->widen($template->getParameter('width'));
            $image->resize($templateSize);
        } elseif ($method === 'height') {
            $templateSize = $image->getSize()->heighten($template->getParameter('height'));
            $image->resize($templateSize);
        } elseif ($method === 'exact') {
            $templateSize = new Box($template->getParameter('width'), $template->getParameter('height'));
            $image->resize($templateSize);
        } elseif ($method === 'fit') {
            $templateSize = new Box($template->getParameter('width'), $template->getParameter('height'));
            $image = $image->thumbnail($templateSize, ImageInterface::THUMBNAIL_INSET);
        } elseif ($method === 'exactFit') {
            $templateSize = new Box($template->getParameter('width'), $template->getParameter('height'));
            $layer = $image->thumbnail($templateSize, ImageInterface::THUMBNAIL_INSET);
            $layerSize = $layer->getSize();

            $palette = new RGB();
            if ($template->hasParameter('backgroundcolor', true)) {
                $color = $palette->color($template->getParameter('backgroundcolor'), 100);
            } else {
                $color = $palette->color('#fff', 0);
            }
            $image = $this->imagine->create($templateSize, $color);
            $image->paste($layer, new Point(
                floor(($templateSize->getWidth() - $layerSize->getWidth()) / 2),
                floor(($templateSize->getHeight() - $layerSize->getHeight()) / 2)
            ));
        } elseif ($method === 'crop') {
            $templateSize = new Box($template->getParameter('width'), $template->getParameter('height'));
            $imageSize = $image->getSize();

            if ($imageSize->getWidth() > $templateSize->getWidth() && $imageSize->getHeight() < $templateSize->getHeight()) {
                $imageSize = $imageSize->heighten($templateSize->getHeight());
                $image->resize($imageSize);
            } elseif ($imageSize->getWidth() < $templateSize->getWidth() && $imageSize->getHeight() > $templateSize->getHeight()) {
                $imageSize = $imageSize->widen($templateSize->getWidth());
                $image->resize($imageSize);
            }

            if (!$templateSize->contains($imageSize)) {
                $ratios = array(
                    $templateSize->getWidth() / $imageSize->getWidth(),
                    $templateSize->getHeight() / $imageSize->getHeight()
                );
                $ratio = max($ratios);
                if (!$imageSize->contains($templateSize)) {
                    $imageSize = new Box(
                        min($imageSize->getWidth(), $templateSize->getWidth()),
                        min($imageSize->getHeight(), $templateSize->getHeight())
                    );
                } else {
                    $imageSize = $imageSize->scale($ratio);
                    $image->resize($imageSize);
                }

                if (($focalPoint = $file->getAttribute('focalpoint')) && !empty($focalPoint['active'])) {
                    $focalPoint = new Point($focalPoint['x'], $focalPoint['y']);

                    $cropX = floor($focalPoint->getX() * $ratio - $templateSize->getWidth() / 2);
                    $cropY = floor($focalPoint->getY() * $ratio - $templateSize->getHeight() / 2);

                    $cropX = max($cropX, 0);
                    $cropY = max($cropY, 0);

                    if ($templateSize->getWidth() + $cropX > $image->getSize()->getWidth()) {
                        $cropX = $image->getSize()->getWidth() - $templateSize->getWidth();
                    }
                    if ($templateSize->getHeight() + $cropY > $image->getSize()->getHeight()) {
                        $cropY = $image->getSize()->getHeight() - $templateSize->getHeight();
                    }

                    $cropPoint = new Point($cropX, $cropY);
                } else {
                    $cropPoint = new Point(
                        max(0, round(($imageSize->getWidth() - $templateSize->getWidth()) / 2)),
                        max(0, round(($imageSize->getHeight() - $templateSize->getHeight()) / 2))
                    );
                }

                $image->crop($cropPoint, $templateSize);
            }
        }

        return $image->save($outFilename, $options);
    }
}
