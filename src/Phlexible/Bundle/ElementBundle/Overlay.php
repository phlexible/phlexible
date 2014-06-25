<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle;

/**
 * Asset
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Overlay
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param string $cacheDir
     */
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * Get asset path
     *
     * @param string $icon
     * @param array  $params
     *
     * @return string
     */
    public function getAssetPath($icon, array $params = array())
    {
        $overlay = array();

        if (!empty($params['status'])) {
            $overlay['status'] = $params['status'];
        }

        if (!empty($params['timer'])) {
            $overlay['timer'] = $params['timer'];
        }

        if (!empty($params['lock'])) {
            $overlay['lock'] = $params['lock'];
        }

        if (!empty($params['instance'])) {
            $overlay['instance'] = $params['instance'];
        }

        if (!empty($params['sort'])) {
            $overlay['sort'] = $params['sort'];
        }

        $fallback = dirname(__DIR__) . '/ElementtypeBundle/Resources/public/elementtypes/icon_notfound.gif';

        if ($icon === null) {
            $filename = $fallback;
        } else {
            $path     =  dirname(__DIR__) . '/ElementtypeBundle/Resources/public/elementtypes/';
            $filename = $path . $icon;

            if (!file_exists($filename)) {
                $filename = $fallback;
            }
        }

        $cacheFilename = $this->cacheDir . '/' . md5(basename($filename) . '__' . implode('__', $overlay)) . '.png';

        if (!file_exists($cacheFilename) || (time() - filemtime($cacheFilename)) > 60 * 60 * 24 * 30) {
            $target = imagecreate(18, 18);
            $black  = imagecolorallocate($target, 0, 0, 0);
            imagecolortransparent($target, $black);

            $iconSource = imagecreatefromgif($filename);
            imagecopy($target, $iconSource, 0, 0, 0, 0, 18, 18);
            imagedestroy($iconSource);

            if (!empty($overlay['status'])) {
                // apply status overlay
                $overlayIcon = imagecreatefromgif(__DIR__ . '/Resources/public/overlays/status_'.$overlay['status'].'.gif');
                imagecopy($target, $overlayIcon, 9, 9, 0, 0, 8, 8);
                imagedestroy($overlayIcon);
            }

            if (!empty($overlay['timer'])) {
                // apply timer overlay
                $overlayIcon = imagecreatefromgif(__DIR__ . '/Resources/public/overlays/timer.gif');
                imagecopy($target, $overlayIcon, 10, 0, 0, 0, 8, 8);
                imagedestroy($overlayIcon);
            }

            if (!empty($overlay['sort'])) {
                // apply timer overlay
                $overlayIcon = imagecreatefromgif(__DIR__ . '/Resources/public/overlays/sort_'.$overlay['sort'].'.gif');
                imagecopy($target, $overlayIcon, 10, 0, 0, 0, 8, 8);
                imagedestroy($overlayIcon);
            }

            if (!empty($overlay['instance'])) {
                // apply alias overlay
                $overlayIcon = imagecreatefromgif(__DIR__ . '/Resources/public/overlays/instance_'.$overlay['instance'].'.gif');
                imagecopy($target, $overlayIcon, 0, 10, 0, 0, 8, 8);
                imagedestroy($overlayIcon);
            }

            if (!empty($overlay['lock'])) {
                // apply lock overlay
                $overlayIcon = imagecreatefromgif(__DIR__ . '/Resources/public/overlays/lock_'.$overlay['lock'].'.gif');
                imagecopy($target, $overlayIcon, 0, 0, 0, 0, 8, 8);
                imagedestroy($overlayIcon);
            }

            if (!file_exists($this->cacheDir)) {
                mkdir($this->cacheDir, 0777, true);
            }

            imagepng($target, $cacheFilename);
        }

        return $cacheFilename;
    }

    public function flush()
    {
        $cacheDir = $this->getCacheDir();

        $files = glob($cacheDir . '*.png');

        foreach($files as $file) {
            unlink($file);
        }
    }
}