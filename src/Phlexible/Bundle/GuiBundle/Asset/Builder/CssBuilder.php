<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Builder;

use Phlexible\Bundle\GuiBundle\Asset\Cache\ResourceCollectionCache;
use Phlexible\Bundle\GuiBundle\Asset\Filter\BaseUrlFilter;
use Phlexible\Bundle\GuiBundle\Asset\Finder\ResourceFinder;
use Phlexible\Bundle\GuiBundle\Asset\MappedAsset;
use Phlexible\Bundle\GuiBundle\Asset\MappedContent\MappedContentBuilder;
use Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface;
use Puli\Repository\Resource\FileResource;

/**
 * CSS builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CssBuilder
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
     * Build stream
     *
     * @param string $baseUrl
     * @param string $basePath
     *
     * @return MappedAsset
     */
    public function build($baseUrl, $basePath)
    {
        $file = $this->cacheDir . '/gui.css';
        $mapFile = $file . '.map';

        $cache = new ResourceCollectionCache($file, $this->debug);

        $resources = $this->resourceFinder->findByType('phlexible/styles');

        if (!$cache->isFresh($resources)) {
            $builder = new MappedContentBuilder();
            $filter = new BaseUrlFilter($baseUrl, $basePath);
            $mappedContent = $builder->build(
                'gui.css',
                $resources,
                function (FileResource $resource) {
                    return preg_match('#^/phlexible/([a-z0-9\-_.]+)/styles/([/A-Za-z0-9\-_.]+\.css)$#', $resource->getPath(), $match)
                        ? $match[1] . '/' . $match[2]
                        : $resource->getPath();
                },
                null,
                function ($content) use ($filter) {
                    return $filter->filter($content);
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
