<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\Extension;

/**
 * Twig image extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImageExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('image', array($this, 'image')),
            new \Twig_SimpleFunction('thumbnail', array($this, 'thumbnail')),
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function image($path)
    {
        if (is_object($path)) {
            return get_class($path);
        }

        return $path;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function thumbnail($path)
    {
        if (is_object($path)) {
            return get_class($path);
        }

        return $path;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'image';
    }
}