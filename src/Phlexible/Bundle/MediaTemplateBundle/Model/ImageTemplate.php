<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Model;

/**
 * Image template
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImageTemplate extends AbstractTemplate
{
    const TYPE_IMAGE = 'image';

    public function __construct()
    {
        $this->setType(self::TYPE_IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultParameters()
    {
        return array(
            'width'           => 0,
            'height'          => 0,
            'method'          => '',
            'scale'           => '',
            'for_web'         => 0,
            'format'          => '',
            'colorspace'      => '',
            'tiffcompression' => '',
            'depth'           => '',
            'quality'         => 0,
            'backgroundcolor' => ''
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedParameters()
    {
        return array(
            'width',
            'height',
            'method',
            'scale',
            'for_web',
            'format',
            'colorspace',
            'tiffcompression',
            'depth',
            'quality',
            'backgroundcolor',
            'optimize_for_web',
        );
    }

    /**
     * Set width
     *
     * @param int $width
     *
     * @return $this
     */
    public function setWidth($width)
    {
        return $this->setParameter('width', $width);
    }

    /**
     * Return width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->getParameter('width');
    }

    /**
     * Set height
     *
     * @param int $height
     *
     * @return $this
     */
    public function setHeight($height)
    {
        return $this->setParameter('height', $height);
    }

    /**
     * Return height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->getParameter('height');
    }

    /**
     * Set method
     *
     * @param string $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        return $this->setParameter('method', $method);
    }

    /**
     * Return method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getParameter('method');
    }
}
