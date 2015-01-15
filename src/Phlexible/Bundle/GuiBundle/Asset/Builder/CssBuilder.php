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
use Phlexible\Bundle\GuiBundle\Compressor\CompressorInterface;
use Puli\Discovery\Api\ResourceDiscovery;
use Puli\Repository\Api\ResourceCollection;
use Puli\Repository\Api\ResourceRepository;
use Puli\Repository\Resource\FileResource;

/**
 * CSS builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CssBuilder
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
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    /**
     * Build stream
     *
     * @param string $baseUrl
     * @param string $basePath
     *
     * @return string
     */
    public function build($baseUrl, $basePath)
    {
        $cache = new ResourceCollectionCache($this->cacheDir . '/gui.css', $this->debug);

        $resources = $this->find();

        if (!$cache->isFresh($resources)) {
            $content = $this->buildCss($resources);

            $filter = new BaseUrlFilter($baseUrl, $basePath);
            $content = $filter->filter($content);

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
        return $this->puliDiscovery->find('phlexible/styles');
    }

    /**
     * @return string
     */
    private function buildCss(ResourceCollection $resources)
    {
        $input = [];

        foreach ($resources as $resource) {
            /* @var $resource FileResource */
            $input[] = $resource->getFilesystemPath();
        }

        $css = '/* Created: ' . date('Y-m-d H:i:s') . ' */';
        foreach ($input as $file) {
            if ($this->debug) {
                $css .= PHP_EOL . "/* File: $file */" . PHP_EOL;
            }
            $css .= file_get_contents($file);
        }

        return $css;
    }
}
