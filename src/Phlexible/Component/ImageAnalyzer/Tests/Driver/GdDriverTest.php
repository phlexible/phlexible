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

use Phlexible\Component\ImageAnalyzer\Driver\GdDriver;

/**
 * GD driver test
 *
 * @author Stephan Wentz <stephan@wentz.it>
 */
class GdDriverTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('gd extension is not installed');
        }
    }

    public function imageProvider()
    {
        return array(
            array(__DIR__ . '/../files/test.jpg', array(
                'analyzer'    => 'Phlexible\Component\ImageAnalyzer\Driver\GdDriver',
                'colorspace'  => 'RGB',
                'compression' => null,
                'depth'       => '8',
                'format'      => 'JPEG',
                'height'      => '350',
                'profiles'    => null,
                'quality'     => null,
                'ratioX'      => '1.3314285714285714',
                'ratioY'      => '0.75107296137339052',
                'resolutionX' => null,
                'resolutionY' => null,
                'type'        => 'TRUECOLOR',
                'units'       => null,
                'width'       => '466',
            )),
            array(__DIR__ . '/../files/test_cmyk.jpg', array(
                'analyzer'    => 'Phlexible\Component\ImageAnalyzer\Driver\GdDriver',
                'colorspace'  => 'CMYK',
                'compression' => null,
                'depth'       => '8',
                'format'      => 'JPEG',
                'height'      => '350',
                'profiles'    => null,
                'quality'     => null,
                'ratioX'      => '1.3314285714285714',
                'ratioY'      => '0.75107296137339052',
                'resolutionX' => null,
                'resolutionY' => null,
                'type'        => 'TRUECOLOR',
                'units'       => null,
                'width'       => '466',
            )),
            array(__DIR__ . '/../files/test.gif', array(
                'analyzer'    => 'Phlexible\Component\ImageAnalyzer\Driver\GdDriver',
                'colorspace'  => 'RGB',
                'compression' => null,
                'depth'       => '8',
                'format'      => 'GIF',
                'height'      => '350',
                'profiles'    => null,
                'quality'     => null,
                'ratioX'      => '1.3314285714285714',
                'ratioY'      => '0.75107296137339052',
                'resolutionX' => null,
                'resolutionY' => null,
                'type'        => 'PALETTE',
                'units'       => null,
                'width'       => '466',
            )),
            array(__DIR__ . '/../files/test.png', array(
                'analyzer'    => 'Phlexible\Component\ImageAnalyzer\Driver\GdDriver',
                'colorspace'  => 'RGB',
                'compression' => null,
                'depth'       => '8',
                'format'      => 'PNG',
                'height'      => '350',
                'profiles'    => null,
                'quality'     => null,
                'ratioX'      => '1.3314285714285714',
                'ratioY'      => '0.75107296137339052',
                'resolutionX' => null,
                'resolutionY' => null,
                'type'        => 'TRUECOLOR',
                'units'       => null,
                'width'       => '466',
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
        $driver = new GdDriver();
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
