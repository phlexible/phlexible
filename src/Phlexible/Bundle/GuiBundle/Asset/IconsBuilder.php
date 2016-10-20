<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Asset;

use Phlexible\Component\Bundler\Asset\Asset;
use Phlexible\Component\Bundler\Cache\PuliResourceCollectionCache;
use Phlexible\Component\Bundler\Compressor\CompressorInterface;
use Phlexible\Component\Bundler\Finder\ResourceFinderInterface;
use Puli\Repository\Resource\FileResource;

/**
 * Icons builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class IconsBuilder
{
    /**
     * @var ResourceFinderInterface
     */
    private $resourceFinder;

    /**
     * @var CompressorInterface
     */
    private $compressor;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param ResourceFinderInterface $resourceFinder
     * @param CompressorInterface     $compressor
     * @param string                  $cacheDir
     * @param bool                    $debug
     */
    public function __construct(
        ResourceFinderInterface $resourceFinder,
        CompressorInterface $compressor,
        $cacheDir,
        $debug)
    {
        $this->resourceFinder = $resourceFinder;
        $this->compressor = $compressor;
        $this->cacheDir = rtrim($cacheDir, '/') . '/';
        $this->debug = $debug;
    }

    /**
     * Get all Stylesheets for the given section
     *
     * @param string $basePath
     *
     * @return Asset
     */
    public function build($basePath)
    {
        $cache = new PuliResourceCollectionCache($this->cacheDir . 'icons.css', $this->debug);

        $resources = $this->resourceFinder->findByType('phlexible/icons');

        if (!$cache->isFresh($resources)) {
            $content = $this->buildIcons($resources, $basePath);

            $cache->write($content);

            if (!$this->debug) {
                $this->compressor->compressFile((string) $cache);
            }
        }

        return new Asset((string) $cache);
    }

    /**
     * @param FileResource[] $resources
     * @param string         $basePath
     *
     * @return string
     */
    private function buildIcons(array $resources, $basePath)
    {
        $data = [];

        foreach ($resources as $resource) {
            if (!preg_match("#^/phlexible/(.+?)/icons/(.*\.(png|gif))$#", $resource->getPath(), $match)) {
                continue;
            }

            $bundle = $match[1];
            $path = $match[2];
            $extension = $match[3];

            $filesystemPath = $resource->getFilesystemPath();
            $file = basename($filesystemPath);
            $name = basename($filesystemPath, ".$extension");

            $size = getimagesize($filesystemPath);
            if ($size[0] != 16 || $size[1] != 16) {
                continue;
            }

            $selector = sprintf('.p-%s-%s-icon', str_replace('phlexible', '', $bundle), $name);

            $data[] = [
                'bundle'    => $bundle,
                'path'      => $path,
                'file'      => $file,
                'name'      => $name,
                'selector'  => $selector,
                'extension' => $extension,
            ];
        }

        $urlTemplate = $basePath . '/bundles/%s/icons/%s';

        $icons = '/* Created: ' . date('Y-m-d H:i:s') . ' */' . PHP_EOL;

        foreach ($data as $row) {
            $url = sprintf($urlTemplate, $row['bundle'], $row['path']);

            $style = $row['selector'] . ' {background-image: url(' . $url . ') !important;}' . PHP_EOL;

            $icons .= $style;
        }

        return $icons;
    }
}
