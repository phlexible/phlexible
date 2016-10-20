<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\ImageAnalyzer;

/**
 * Image analysis result
 *
 * @author Stephan Wentz <stephan@wentz.it>
 */
class ImageInfo
{
    /**
     * @var string
     */
    private $analyzer;

    /**
     * @var integer
     */
    private $width;

    /**
     * @var integer
     */
    private $height;

    /**
     * @var string
     */
    private $format;

    /**
     * @var integer
     */
    private $colors;

    /**
     * @var string
     */
    private $depth;

    /**
     * @var string
     */
    private $compression;

    /**
     * @var integer
     */
    private $quality;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $colorspace;

    /**
     * @var float
     */
    private $ratioX;

    /**
     * @var float
     */
    private $ratioY;

    /**
     * @var int
     */
    private $resolutionX;

    /**
     * @var int
     */
    private $resolutionY;

    /**
     * @var string
     */
    private $units;

    /**
     * @var array
     */
    private $profiles = array();

    /**
     * @return string
     */
    public function getAnalyzer()
    {
        return $this->analyzer;
    }

    /**
     * @param string $analyzer
     *
     * @return $this
     */
    public function setAnalyzer($analyzer)
    {
        $this->analyzer = $analyzer;

        return $this;
    }

    /**
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->width && $this->height ? $this->width . 'x' . $this->height : null;
    }

    /**
     * @param integer $width
     * @param integer $height
     *
     * @return $this
     */
    public function setSize($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->ratioX = $width / $height;
        $this->ratioY = $height / $width;

        return $this;
    }

    /**
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param integer $height
     *
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = strtoupper($format);

        return $this;
    }

    /**
     * @return int
     */
    public function getColors()
    {
        return $this->colors;
    }

    /**
     * @param int $colors
     *
     * @return $this
     */
    public function setColors($colors)
    {
        $this->colors = $colors;

        return $this;
    }

    /**
     * @return string
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param string $depth
     *
     * @return $this
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompression()
    {
        return $this->compression;
    }

    /**
     * @param string $compression
     *
     * @return $this
     */
    public function setCompression($compression)
    {
        $this->compression = $compression;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * @param int $quality
     *
     * @return $this
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getColorspace()
    {
        return $this->colorspace;
    }

    /**
     * @param string $colorspace
     *
     * @return $this
     */
    public function setColorspace($colorspace)
    {
        $this->colorspace = strtoupper($colorspace);

        return $this;
    }

    /**
     * @return float
     */
    public function getRatioX()
    {
        return $this->ratioX;
    }

    /**
     * @return float
     */
    public function getRatioY()
    {
        return $this->ratioY;
    }

    /**
     * @return float
     */
    public function getResolutionX()
    {
        return $this->resolutionX;
    }

    /**
     * @return float
     */
    public function getResolutionY()
    {
        return $this->resolutionY;
    }

    /**
     * @return string
     */
    public function getResolution()
    {
        return $this->resolutionX && $this->resolutionY ? $this->resolutionX . 'x' . $this->resolutionY : null;
    }

    /**
     * @param float $resolutionX
     * @param float $resolutionY
     *
     * @return $this
     */
    public function setResolution($resolutionX, $resolutionY)
    {
        $this->resolutionX = $resolutionX;
        $this->resolutionY = $resolutionY;

        return $this;
    }

    /**
     * @return string
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * @param string $units
     *
     * @return $this
     */
    public function setUnits($units)
    {
        $this->units = $units;

        return $this;
    }

    /**
     * @return array
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * @param array $profiles
     *
     * @return $this
     */
    public function setProfiles(array $profiles = null)
    {
        $this->profiles = $profiles;

        return $this;
    }
}
