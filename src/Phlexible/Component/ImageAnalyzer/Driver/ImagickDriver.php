<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\ImageAnalyzer\Driver;

use Phlexible\Component\ImageAnalyzer\ImageInfo;

/**
 * Imagick extension image analyzer driver.
 *
 * @author Stephan Wentz <stephan@wentz.it>
 */
class ImagickDriver implements DriverInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAvailable($filename = null)
    {
        return extension_loaded('imagick');
    }

    /**
     * {@inheritdoc}
     */
    public function analyze($filename)
    {
        $imagick = new \Imagick($filename);
        $imageInfo = new ImageInfo();

        $identify = $imagick->identifyImage(false);

        $imageInfo
            ->setAnalyzer(get_class($this))
            ->setSize($imagick->getImageWidth(), $imagick->getImageHeight())
            ->setResolution($imagick->getImageResolution()['x'], $imagick->getImageResolution()['y'])
            ->setUnits($this->mapUnits($imagick->getImageUnits()))
            ->setFormat($imagick->getImageFormat())
            ->setColors($imagick->getImageColors())
            ->setType($this->mapType($imagick->getImageType()))
            ->setColorspace($this->mapColorspace($imagick->getImageColorspace()))
            ->setDepth($imagick->getImageDepth())
            ->setCompression($identify['compression'])
            ->setQuality($imagick->getImageCompressionQuality())
            ->setProfiles($imagick->getImageProfiles('*', false));

        return $imageInfo;
    }

    /**
     * @param int $units
     *
     * @return null|string
     */
    private function mapUnits($units)
    {
        switch ($units) {
            case \Imagick::RESOLUTION_PIXELSPERCENTIMETER:
                return 'PixelsPerCentimeter';
            case \Imagick::RESOLUTION_PIXELSPERINCH:
                return 'PixelsPerInch';
            case \Imagick::RESOLUTION_UNDEFINED:
                return null;
        }
    }

    /**
     * @param int $type
     *
     * @return null|string
     */
    private function mapType($type)
    {
        switch ($type) {
            case \Imagick::IMGTYPE_BILEVEL:
                return 'BILEVEL';
            case \Imagick::IMGTYPE_GRAYSCALE:
                return 'GRAYSCALE';
            case \Imagick::IMGTYPE_GRAYSCALEMATTE:
                return 'GRAYSCALEMATTE';
            case \Imagick::IMGTYPE_PALETTE:
                return 'PALETTE';
            case \Imagick::IMGTYPE_PALETTEMATTE:
                return 'PALETTEMATTE';
            case \Imagick::IMGTYPE_TRUECOLOR:
                return 'TRUECOLOR';
            case \Imagick::IMGTYPE_TRUECOLORMATTE:
                return 'TRUECOLORMATTE';
            case \Imagick::IMGTYPE_COLORSEPARATION:
                return 'COLORSEPARATION';
            case \Imagick::IMGTYPE_COLORSEPARATIONMATTE:
                return 'COLORSEPARATIONMATTE';
            case \Imagick::IMGTYPE_OPTIMIZE:
                return 'OPTIMIZE';
            case \Imagick::IMGTYPE_UNDEFINED:
            default:
                return null;
        }
    }

    /**
     * @param int $colorspace
     *
     * @return null|string
     */
    private function mapColorspace($colorspace)
    {
        switch ($colorspace) {
            case \Imagick::COLORSPACE_CMY:
                return 'CMY';
            case \Imagick::COLORSPACE_CMYK:
                return 'CMYK';
            case \Imagick::COLORSPACE_GRAY:
                return 'GRAY';
            case \Imagick::COLORSPACE_HSB:
                return 'HSB';
            case \Imagick::COLORSPACE_HSL:
                return 'HSL';
            case \Imagick::COLORSPACE_HWB:
                return 'HWB';
            case \Imagick::COLORSPACE_LAB:
                return 'LAB';
            case \Imagick::COLORSPACE_LOG:
                return 'LOG';
            case \Imagick::COLORSPACE_OHTA:
                return 'OHTA';
            case \Imagick::COLORSPACE_REC601LUMA:
                return 'REC601LUMA';
            case \Imagick::COLORSPACE_RGB:
                return 'RGB';
            case \Imagick::COLORSPACE_REC709LUMA:
                return 'REC709LUMA';
            case \Imagick::COLORSPACE_SRGB:
                return 'SRGB';
            case \Imagick::COLORSPACE_TRANSPARENT:
                return 'TRANSPARENT';
            case \Imagick::COLORSPACE_XYZ:
                return 'XYZ';
            case \Imagick::COLORSPACE_YCBCR:
                return 'YCBCR';
            case \Imagick::COLORSPACE_YCC:
                return 'YCC';
            case \Imagick::COLORSPACE_YIQ:
                return 'YIQ';
            case \Imagick::COLORSPACE_YPBPR:
                return 'YPBPR';
            case \Imagick::COLORSPACE_YUV:
                return 'YUV';
            case \Imagick::COLORSPACE_UNDEFINED:
            default:
                return null;
        }
    }
}
