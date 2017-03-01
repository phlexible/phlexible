<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\ImageAnalyzer\Tests\Driver;

use Phlexible\Component\ImageAnalyzer\Driver\ImagickDriver;
use PHPUnit\Framework\TestCase;

/**
 * Imagick driver test.
 *
 * @author Stephan Wentz <stephan@wentz.it>
 *
 * @covers \Phlexible\Component\ImageAnalyzer\Driver\ImagickDriver
 */
class ImagickDriverTest extends TestCase
{
    public function setUp()
    {
        if (!extension_loaded('imagick')) {
            $this->markTestSkipped('imagick extension is not installed');
        }
    }

    public function imageProvider()
    {
        return array(
            array(__DIR__.'/../files/test.tif', array(
                'analyzer' => ImagickDriver::class,
                'colorspace' => 'SRGB',
                'compression' => 'None',
                'depth' => '8',
                'format' => 'TIFF',
                'height' => '350',
                'profiles' => array(),
                'quality' => '0',
                'ratioX' => '1.3314285714285714',
                'ratioY' => '0.75107296137339052',
                'resolutionX' => '0.0',
                'resolutionY' => '0.0',
                'type' => 'TRUECOLOR',
                'units' => 'PixelsPerInch',
                'width' => '466',
            )),
            array(__DIR__.'/../files/test.jpg', array(
                'analyzer' => ImagickDriver::class,
                'colorspace' => 'SRGB',
                'compression' => 'JPEG',
                'depth' => '8',
                'format' => 'JPEG',
                'height' => '350',
                'profiles' => array(),
                'quality' => '91',
                'ratioX' => '1.3314285714285714',
                'ratioY' => '0.75107296137339052',
                'resolutionX' => '0.0',
                'resolutionY' => '0.0',
                'type' => 'TRUECOLOR',
                'units' => null,
                'width' => '466',
            )),
            array(__DIR__.'/../files/test_cmyk.jpg', array(
                'analyzer' => ImagickDriver::class,
                'colorspace' => 'CMYK',
                'compression' => 'JPEG',
                'depth' => '8',
                'format' => 'JPEG',
                'height' => '350',
                'profiles' => array(),
                'quality' => '91',
                'ratioX' => '1.3314285714285714',
                'ratioY' => '0.75107296137339052',
                'resolutionX' => '0.0',
                'resolutionY' => '0.0',
                'type' => 'COLORSEPARATION',
                'units' => null,
                'width' => '466',
            )),
            array(__DIR__.'/../files/test.gif', array(
                'analyzer' => ImagickDriver::class,
                'colorspace' => 'SRGB',
                'compression' => 'LZW',
                'depth' => '8',
                'format' => 'GIF',
                'height' => '350',
                'profiles' => array(),
                'quality' => '0',
                'ratioX' => '1.3314285714285714',
                'ratioY' => '0.75107296137339052',
                'resolutionX' => '0',
                'resolutionY' => '0',
                'type' => 'PALETTE',
                'units' => null,
                'width' => '466',
            )),
            array(__DIR__.'/../files/test.png', array(
                'analyzer' => ImagickDriver::class,
                'colorspace' => 'SRGB',
                'compression' => 'Zip',
                'depth' => '8',
                'format' => 'PNG',
                'height' => '350',
                'profiles' => array(),
                'quality' => '0',
                'ratioX' => '1.3314285714285714',
                'ratioY' => '0.75107296137339052',
                'resolutionX' => '0.0',
                'resolutionY' => '0.0',
                'type' => 'TRUECOLOR',
                'units' => null,
                'width' => '466',
            )),
        );
    }

    /**
     * @param string $file
     * @param array  $expected
     *
     * @dataProvider imageProvider
     */
    public function testDriver($file, $expected)
    {
        $driver = new ImagickDriver();
        $imageInfo = $driver->analyze($file);

        $this->assertEquals($expected['analyzer'], $imageInfo->getAnalyzer());
        $this->assertEquals($expected['colorspace'], $imageInfo->getColorspace());
        $this->assertEquals($expected['compression'], $imageInfo->getCompression());
        $this->assertEquals($expected['depth'], $imageInfo->getDepth());
        $this->assertEquals($expected['format'], $imageInfo->getFormat());
        $this->assertEquals($expected['height'], $imageInfo->getHeight());
        $this->assertEquals($expected['profiles'], $imageInfo->getProfiles());
        $this->assertEquals($expected['quality'], $imageInfo->getQuality());
        $this->assertEquals($expected['ratioX'], $imageInfo->getRatioX());
        $this->assertEquals($expected['ratioY'], $imageInfo->getRatioY());
        $this->assertEquals($expected['resolutionX'], $imageInfo->getResolutionX());
        $this->assertEquals($expected['resolutionY'], $imageInfo->getResolutionY());
        $this->assertEquals($expected['type'], $imageInfo->getType());
        $this->assertEquals($expected['units'], $imageInfo->getUnits());
        $this->assertEquals($expected['width'], $imageInfo->getWidth());
    }
}
