<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Builder;

use Phlexible\Bundle\GuiBundle\Asset\Cache\ResourceCollectionCache;
use Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface;
use Puli\Discovery\Api\Binding\ResourceBinding;
use Puli\Discovery\Api\ResourceDiscovery;
use Puli\Repository\Api\ResourceCollection;

/**
 * Icons builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class IconsBuilder
{
    /**
     * @var ResourceDiscovery
     */
    private $puliDiscovery;

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
     * @param ResourceDiscovery   $puliDiscovery
     * @param CompressorInterface $compressor
     * @param string              $cacheDir
     * @param bool                $debug
     */
    public function __construct(
        ResourceDiscovery $puliDiscovery,
        CompressorInterface $compressor,
        $cacheDir,
        $debug)
    {
        $this->puliDiscovery = $puliDiscovery;
        $this->compressor = $compressor;
        $this->cacheDir = rtrim($cacheDir, '/') . '/';
        $this->debug = $debug;
    }

    /**
     * Get all Stylesheets for the given section
     *
     * @param string $baseUrl
     * @param string $basePath
     *
     * @return string
     */
    public function build($baseUrl, $basePath)
    {
        $cache = new ResourceCollectionCache($this->cacheDir . '/icons.css', $this->debug);

        $bindings = $this->findBindings();

        if (!$cache->isFresh($bindings)) {
            $content = $this->buildIcons($bindings, $baseUrl, $basePath);

            $cache->write($content);

            if (!$this->debug) {
                $this->compressor->compressFile((string) $cache);
            }

        }

        return (string) $cache;
    }

    /**
     * @return ResourceBinding[]
     */
    private function findBindings()
    {
        return $this->puliDiscovery->findByType('phlexible/icons');
    }

    /**
     * @param ResourceBinding[] $bindings
     * @param string            $baseUrl
     * @param string            $basePath
     *
     * @return string
     */
    private function buildIcons(array $bindings, $baseUrl, $basePath)
    {
        $data = [];

        foreach ($bindings as $binding) {
            foreach ($binding->getResources() as $resource) {
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
