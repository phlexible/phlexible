<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\Builder;

use Phlexible\Component\GuiAsset\Asset\MappedAsset;
use Phlexible\Component\GuiAsset\Cache\PuliResourceCollectionCache;
use Phlexible\Component\GuiAsset\Compressor\CompressorInterface;
use Phlexible\Component\GuiAsset\ContentBuilder\MappedContentBuilder;
use Phlexible\Component\GuiAsset\Finder\ResourceFinderInterface;
use Phlexible\Component\GuiAsset\ResourceResolver\ResolvedResources;
use Phlexible\Component\GuiAsset\ResourceResolver\ResourceResolverInterface;

/**
 * Resolving builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class ResolvingBuilder
{
    /**
     * @var ResourceFinderInterface
     */
    private $resourceFinder;

    /**
     * @var ResourceResolverInterface
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
     * @param ResourceFinderInterface   $resourceFinder
     * @param ResourceResolverInterface $resourceResolver
     * @param MappedContentBuilder      $contentBuilder
     * @param CompressorInterface       $compressor
     * @param string                    $cacheDir
     * @param bool                      $debug
     */
    public function __construct(
        ResourceFinderInterface $resourceFinder,
        ResourceResolverInterface $resourceResolver,
        MappedContentBuilder $contentBuilder,
        CompressorInterface $compressor,
        $cacheDir,
        $debug
    ) {
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
     * @return \Phlexible\Component\GuiAsset\Asset\MappedAsset
     */
    public function build()
    {
        $file = $this->cacheDir . '/' . $this->getFilename();
        $mapFile = $file . '.map';

        $cache = new PuliResourceCollectionCache($file, $this->isDebug());

        $resources = $this->resourceFinder->findByType($this->getType());

        if (!$cache->isFresh($resources)) {
            $resolvedResources = $this->resourceResolver->resolve($resources);
            $mappedContent = $this->contentBuilder->build(
                $this->getFilename(),
                $resolvedResources,
                array($this, 'sanitizePath'),
                array($this, 'prefixContent')
            );

            $cache->write($mappedContent->getContent());
            file_put_contents($mapFile, $mappedContent->getMap());

            if (!$this->debug) {
                $this->compressor->compressFile($file);
            }

        }

        return new MappedAsset($file, $mapFile);
    }

    /**
     * @return bool
     */
    protected function isDebug()
    {
        return $this->debug;
    }

    /**
     * @return string
     */
    abstract protected function getFilename();

    /**
     * @return string
     */
    abstract protected function getType();

    /**
     * @param string $path
     *
     * @return string
     */
    abstract protected function sanitizePath($path);

    /**
     * @param ResolvedResources $resources
     *
     * @return string
     */
    abstract protected function prefixContent(ResolvedResources $resources);
}
