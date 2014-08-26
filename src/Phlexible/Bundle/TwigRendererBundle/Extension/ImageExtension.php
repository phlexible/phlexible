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
     * @param string $image
     *
     * @return string
     */
    public function image($image)
    {
        if (!$image) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        // deliver original file
        $src = /*$request->getBaseUrl() . */'/media/' . $fileId . '.jpg';

        return $src;
    }

    /**
     * @param string $image
     * @param string $template
     *
     * @return string
     */
    public function thumbnail($image, $template = null)
    {
        if (!$image && !$template) {
            return '';
        }

        $parts = explode(';', $image);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        if ($template === null) {
            // deliver original file
            $src = /*$request->getBaseUrl() . */'/media/' . $fileId . '/' . $template . '.jpg';
        } else {
            // deliver thumbnail
            $src = /*$request->getBaseUrl() . */'/media/' . $fileId . '/' . $template . '.jpg';
        }

        return $src;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'image';
    }
}