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
use Puli\Repository\Api\ResourceCollection;
use Puli\Repository\Api\ResourceRepository;

/**
 * Icons builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class IconsBuilder
{
    /**
     * @var ResourceRepository
     */
    private $puliRepository;

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
     * @param ResourceRepository  $puliRepository
     * @param CompressorInterface $compressor
     * @param string              $cacheDir
     * @param bool                $debug
     */
    public function __construct(
        ResourceRepository $puliRepository,
        CompressorInterface $compressor,
        $cacheDir,
        $debug)
    {
        $this->puliRepository = $puliRepository;
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

        $resources = $this->find();

        if (!$cache->isFresh($resources)) {
            $content = $this->buildIcons($resources, $baseUrl, $basePath);

            $cache->write($content);

            if (!$this->debug) {
                $this->compressor->compressFile((string) $cache);
            }

        }

        return file_get_contents((string) $cache);
    }

    /**
     * @return ResourceCollection
     */
    private function find()
    {
        return $this->puliRepository->find('/phlexible/icons/*/*.*');
    }

    /**
     * @param ResourceCollection $resources
     * @param string             $baseUrl
     * @param string             $basePath
     *
     * @return string
     */
    private function buildIcons(ResourceCollection $resources, $baseUrl, $basePath)
    {
        $data = [];

        foreach ($resources as $resource) {
            if (!preg_match("#^/phlexible/icons/(.+?)/(.*\.(png|gif))$#", $resource->getPath(), $match)) {
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
