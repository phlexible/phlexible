<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\Extension;

/**
 * Twig url extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UrlExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('url', array($this, 'url')),
            new \Twig_SimpleFunction('link', array($this, 'link')),
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function url($path)
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
    public function link($path)
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
        return 'url';
    }
}