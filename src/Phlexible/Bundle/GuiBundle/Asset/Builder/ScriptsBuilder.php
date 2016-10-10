<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Builder;

use Phlexible\Bundle\GuiBundle\Asset\Cache\ResourceCollectionCache;
use Phlexible\Bundle\GuiBundle\Asset\Finder\ResourceFinder;
use Phlexible\Bundle\GuiBundle\Asset\MappedAsset;
use Phlexible\Bundle\GuiBundle\Asset\MappedContent\MappedContentBuilder;
use Phlexible\Bundle\GuiBundle\Asset\ResourceResolver\ScriptsResourceResolver;
use Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface;
use Puli\Repository\Resource\FileResource;

/**
 * Scripts builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ScriptsBuilder
{
    /**
     * @var ResourceFinder
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
     * @param ResourceFinder      $resourceFinder
     * @param CompressorInterface $compressor
     * @param string              $cacheDir
     * @param bool                $debug
     */
    public function __construct(
        ResourceFinder $resourceFinder,
        CompressorInterface $compressor,
        $cacheDir,
        $debug)
    {
        $this->resourceFinder = $resourceFinder;
        $this->compressor = $compressor;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    /**
     * Get all javascripts for the given section
     *
     * @return MappedAsset
     */
    public function build()
    {
        $file = $this->cacheDir . '/gui.js';
        $mapFile = $file . '.map';

        $cache = new ResourceCollectionCache($file, $this->debug);

        $resources = $this->resourceFinder->findByType('phlexible/scripts');

        if (!$cache->isFresh($resources)) {
            $resolver = new ScriptsResourceResolver();
            $resolvedResources = $resolver->resolve($resources);
            $debug = $this->debug;
            $unusedResources = $resolvedResources->getUnusedResources();
            $builder = new MappedContentBuilder();
            $mappedContent = $builder->build(
                'gui.js',
                $resolvedResources->getResources(),
                function (FileResource $resource) {
                    return preg_match('#^/phlexible/([a-z0-9\-_.]+)/scripts/([/A-Za-z0-9\-_.]+\.js)$#', $resource->getPath(), $match)
                        ? $match[1] . '/' . $match[2]
                        : $resource->getPath();
                },
                function () use ($debug, $unusedResources) {
                    if (!$debug) {
                        return '';
                    }
                    $prefix = '/* ' . PHP_EOL;
                    $prefix .= ' * Unused resources:' . PHP_EOL;
                    foreach ($unusedResources as $unusedResource) {
                        $prefix .= ' * ' . $unusedResource->getPath() . PHP_EOL;
                    }

                    $prefix .= ' */' . PHP_EOL;
                    return $prefix;
                }
            );

            $cache->write($mappedContent->getContent());
            file_put_contents($mapFile, $mappedContent->getMap());

            if (!$this->debug) {
                $this->compressor->compressFile($file);
            }

        }

        return new MappedAsset($file, $mapFile);
    }
}
