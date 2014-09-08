<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\Extension;

use Symfony\Component\Routing\RouterInterface;

/**
 * Twig image extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImageExtension extends \Twig_Extension
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('image', array($this, 'image')),
            new \Twig_SimpleFunction('icon', array($this, 'icon')),
            new \Twig_SimpleFunction('thumbnail', array($this, 'thumbnail')),
            new \Twig_SimpleFunction('download', array($this, 'download')),
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
        $src = $this->router->generate('frontendmedia_thumbnail', array('id' => $fileId));

        return $src;
    }

    /**
     * @param string $image
     *
     * @return string
     */
    public function icon($image)
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

        $src = $this->router->generate('frontendmedia_icon', array('id' => $fileId));

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
            $src = $this->router->generate('frontendmedia_inline', array('id' => $fileId));
        } else {
            // deliver thumbnail
            $src = $this->router->generate('frontendmedia_thumbnail', array('id' => $fileId, 'template' => $template));
        }

        return $src;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function download($file)
    {
        if (!$file) {
            return '';
        }

        $parts = explode(';', $file);
        $fileId = $parts[0];
        $fileVersion = 1;
        if (isset($parts[1])) {
            $fileVersion = $parts[1];
        }

        $src = $this->router->generate('frontendmedia_download', array('id' => $fileId));

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