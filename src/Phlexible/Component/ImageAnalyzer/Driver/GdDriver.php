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
 * GD image analyzer driver.
 *
 * @author Stephan Wentz <stephan@wentz.it>
 */
class GdDriver implements DriverInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAvailable($filename = null)
    {
        return extension_loaded('gd');
    }

    /**
     * {@inheritdoc}
     */
    public function analyze($filename)
    {
        $imageSize = getimagesize($filename);
        $imageInfo = new ImageInfo();

        $type = null;
        $colors = null;
        if (in_array($imageSize[2], array(IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG))) {
            if ($imageSize[2] === IMAGETYPE_JPEG) {
                $gd = imagecreatefromjpeg($filename);
            } elseif ($imageSize[2] === IMAGETYPE_GIF) {
                $gd = imagecreatefromgif($filename);
            } elseif ($imageSize[2] === IMAGETYPE_PNG) {
                $gd = imagecreatefrompng($filename);
            }
            $type = imageistruecolor($gd) ? 'TRUECOLOR' : 'PALETTE';
            $colors = imagecolorstotal($gd);
        }

        $imageInfo
            ->setAnalyzer(get_class($this))
            ->setSize($imageSize[0], $imageSize[1])
            ->setResolution(null, null)
            ->setUnits(null)
            ->setFormat($this->mapFormat($imageSize[2]))
            ->setColors($colors)
            ->setType($type)
            ->setColorspace(!empty($imageSize['channels']) ? ($imageSize['channels'] === 4 ? 'CMYK' : 'RGB') : 'RGB')
            ->setDepth($imageSize['bits'])
            ->setCompression(null)
            ->setQuality(null)
            ->setProfiles(null);

        return $imageInfo;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function mapFormat($type)
    {
        switch ($type) {
            case IMAGETYPE_BMP:
                return 'BMP';
            case IMAGETYPE_COUNT:
                return 'COUNT';
            case IMAGETYPE_GIF:
                return 'GIF';
            case IMAGETYPE_ICO:
                return 'ICO';
            case IMAGETYPE_IFF:
                return 'IFF';
            case IMAGETYPE_JB2:
                return 'JB2';
            case IMAGETYPE_JP2:
                return 'JP2';
            case IMAGETYPE_JPC:
                return 'JPC';
            case IMAGETYPE_JPEG:
                return 'JPEG';
            case IMAGETYPE_JPEG2000:
                return 'JPEG2000';
            case IMAGETYPE_JPX:
                return 'JPX';
            case IMAGETYPE_PNG:
                return 'PNG';
            case IMAGETYPE_PSD:
                return 'PSD';
            case IMAGETYPE_SWC:
                return 'SWC';
            case IMAGETYPE_SWF:
                return 'SWF';
            case IMAGETYPE_TIFF_II:
                return 'TIFF_II';
            case IMAGETYPE_TIFF_MM:
                return 'TIFF_MM';
            case IMAGETYPE_WBMP:
                return 'WBMP';
            case IMAGETYPE_XBM:
                return 'XBM';
            case IMAGETYPE_UNKNOWN:
            default:
                return 'UNKNOWN';
        }
    }
}
