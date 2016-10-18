<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Builder;

use Phlexible\Bundle\GuiBundle\Asset\Cache\ResourceCollectionCache;
use Phlexible\Bundle\GuiBundle\Asset\Finder\ResourceFinderInterface;
use Phlexible\Bundle\GuiBundle\Asset\MappedAsset;
use Phlexible\Bundle\GuiBundle\Asset\MappedContent\MappedContentBuilder;
use Phlexible\Bundle\GuiBundle\Asset\ResourceResolver\ScriptsResourceResolver;
use Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface;

/**
 * Scripts builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ScriptsBuilder
{
    /**
     * @var ResourceFinderInterface
     */
    private $resourceFinder;

    /**
     * @var ScriptsResourceResolver
     */
    private $resourceResolver;

    /**
     * @var MappedContentBuilder
     */
    private $contentBuilder;

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
     * @param ScriptsResourceResolver $resourceResolver
     * @param MappedContentBuilder    $contentBuilder
     * @param CompressorInterface     $compressor
     * @param string                  $cacheDir
     * @param bool                    $debug
     */
    public function __construct(
        ResourceFinderInterface $resourceFinder,
        ScriptsResourceResolver $resourceResolver,
        MappedContentBuilder $contentBuilder,
        CompressorInterface $compressor,
        $cacheDir,
        $debug)
    {
        $this->resourceFinder = $resourceFinder;
        $this->resourceResolver = $resourceResolver;
        $this->contentBuilder = $contentBuilder;
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
            $resolvedResources = $this->resourceResolver->resolve($resources);
            $debug = $this->debug;
            $unusedResources = $resolvedResources->getUnusedResources();
            $mappedContent = $this->contentBuilder->build(
                'gui.js',
                $resolvedResources->getResources(),
                function ($path) {
                    return preg_match('#^/phlexible/([a-z0-9\-_.]+)/scripts/([/A-Za-z0-9\-_.]+\.js)$#', $path, $match)
                        ? $match[1] . '/' . $match[2]
                        : $path;
                },
                function () use ($debug, $unusedResources) {
                    $prefix = '';
                    $prefix .= '/* JS created on: ' . date('Y-m-d H:i:s') . ' */' . PHP_EOL;
                    if (!$debug) {
                        return $prefix;
                    }
                    $prefix .= '/* ' . PHP_EOL;
                    $prefix .= ' * Unused resources:' . PHP_EOL;
                    foreach ($unusedResources as $unusedResource) {
                        //$prefix .= ' * ' . $unusedResource->getPath() . PHP_EOL;
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
