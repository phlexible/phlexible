<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Icon;

use Symfony\Component\Config\FileLocatorInterface;

/**
 * Icon builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class IconBuilder
{
    /**
     * @var FileLocatorInterface
     */
    private $locator;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param FileLocatorInterface $locator
     * @param string               $cacheDir
     */
    public function __construct(FileLocatorInterface $locator, $cacheDir)
    {
        $this->locator = $locator;
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
    public function getAssetPath($icon, array $params = [])
    {
        $overlay = [];

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

        $fallback = $this->locator->locate('@PhlexibleElementtypeBundle/Resources/public/elementtypes/icon_notfound.gif');

        if ($icon === null) {
            $filename = $fallback;
        } else {
            $prefix = '@PhlexibleElementtypeBundle/Resources/public/elementtypes/';
            $filename = $this->locator->locate($prefix. $icon);

            if (!file_exists($filename)) {
                $filename = $fallback;
            }
        }

        $cacheFilename = $this->cacheDir . '/' . md5(basename($filename) . '__' . implode('__', $overlay)) . '.png';

        if (!file_exists($cacheFilename) || (time() - filemtime($cacheFilename)) > 60 * 60 * 24 * 30) {
            $target = imagecreate(18, 18);
            $black = imagecolorallocate($target, 0, 0, 0);
            imagecolortransparent($target, $black);

            $iconSource = imagecreatefromgif($filename);
            imagecopy($target, $iconSource, 0, 0, 0, 0, 18, 18);
            imagedestroy($iconSource);

            $overlayDir = '@PhlexibleElementBundle/Resources/public/overlays/';

            if (!empty($overlay['status'])) {
                // apply status overlay
                $overlayIcon = imagecreatefromgif(
                    $this->locator->locate($overlayDir . 'status_' . $overlay['status'] . '.gif')
                );
                imagecopy($target, $overlayIcon, 9, 9, 0, 0, 8, 8);
                imagedestroy($overlayIcon);
            }

            if (!empty($overlay['timer'])) {
                // apply timer overlay
                $overlayIcon = imagecreatefromgif(
                    $this->locator->locate($overlayDir . 'timer.gif')
                );
                imagecopy($target, $overlayIcon, 10, 0, 0, 0, 8, 8);
                imagedestroy($overlayIcon);
            }

            if (!empty($overlay['sort'])) {
                // apply timer overlay
                $overlayIcon = imagecreatefromgif(
                    $this->locator->locate($overlayDir . 'sort_' . $overlay['sort'] . '.gif')
                );
                imagecopy($target, $overlayIcon, 10, 0, 0, 0, 8, 8);
                imagedestroy($overlayIcon);
            }

            if (!empty($overlay['instance'])) {
                // apply alias overlay
                $overlayIcon = imagecreatefromgif(
                    $this->locator->locate($overlayDir . 'instance_' . $overlay['instance'] . '.gif')
                );
                imagecopy($target, $overlayIcon, 0, 10, 0, 0, 8, 8);
                imagedestroy($overlayIcon);
            }

            if (!empty($overlay['lock'])) {
                // apply lock overlay
                $overlayIcon = imagecreatefromgif(
                    $this->locator->locate($overlayDir . 'lock_' . $overlay['lock'] . '.gif')
                );
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

    /**
     * Flush
     */
    public function flush()
    {
        $cacheDir = $this->getCacheDir();

        $files = glob($cacheDir . '*.png');

        foreach ($files as $file) {
            unlink($file);
        }
    }
}
