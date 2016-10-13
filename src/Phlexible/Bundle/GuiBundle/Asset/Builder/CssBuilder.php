<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Builder;

use Phlexible\Bundle\GuiBundle\Asset\Cache\ResourceCollectionCache;
use Phlexible\Bundle\GuiBundle\Asset\Filter\BaseUrlContentFilter;
use Phlexible\Bundle\GuiBundle\Asset\Finder\ResourceFinderInterface;
use Phlexible\Bundle\GuiBundle\Asset\MappedAsset;
use Phlexible\Bundle\GuiBundle\Asset\MappedContent\MappedContentBuilder;
use Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface;

/**
 * CSS builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CssBuilder
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
        $debug
    ) {
        $this->resourceFinder = $resourceFinder;
        $this->compressor = $compressor;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    /**
     * Build css file
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
            $filter = new BaseUrlContentFilter($baseUrl, $basePath);
            $mappedContent = $builder->build(
                'gui.css',
                $resources,
                function ($path) {
                    return preg_match('#^/phlexible/([a-z0-9\-_.]+)/styles/([/A-Za-z0-9\-_.]+\.css)$#', $path, $match)
                        ? $match[1] . '/' . $match[2]
                        : $path;
                },
                function () {
                    $prefix = '/* CSS created on: ' . date('Y-m-d H:i:s') . ' */' . PHP_EOL;
                    return $prefix;
                },
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
