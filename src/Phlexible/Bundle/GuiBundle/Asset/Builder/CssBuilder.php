<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Builder;

use Assetic\Asset\AssetCache;
use Assetic\Asset\AssetCollection;
use Assetic\Cache\FilesystemCache;
use Phlexible\Bundle\GuiBundle\Asset\Filter\BaseUrlFilter;
use Phlexible\Bundle\GuiBundle\Asset\Filter\FilenameFilter;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderCollection;
use Phlexible\Bundle\GuiBundle\Compressor\CssCompressor\CssCompressorInterface;

/**
 * CSS builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CssBuilder
{
    /**
     * @var AssetProviderCollection
     */
    private $assetProviders;

    /**
     * @var CssCompressorInterface
     */
    private $cssCompressor;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param AssetProviderCollection $assetProviders
     * @param CssCompressorInterface  $cssCompressor
     * @param string                  $cacheDir
     * @param bool                    $debug
     */
    public function __construct(
        AssetProviderCollection $assetProviders,
        CssCompressorInterface $cssCompressor,
        $cacheDir,
        $debug)
    {
        $this->assetProviders = $assetProviders;
        $this->cssCompressor = $cssCompressor;
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
    public function get($baseUrl, $basePath)
    {
        $filters = array(
            new BaseUrlFilter($baseUrl, $basePath),
        );

        if (!$this->debug) {
            $filters[] = $this->cssCompressor;
            $filters[] = new FilenameFilter();
            //$filters[] = new Assetic\Filter\Yui\JsCompressorFilter('/Users/swentz/Sites/ofcs/hoffmann/app/Resources/java/yuicompressor-2.4.7.jar');
            //$filters[] = new Assetic\Filter\CssMinFilter();
        }

        $scripts = new AssetCollection(array(), $filters);

        foreach ($this->assetProviders->getAssetProviders() as $assetProvider) {
            $collection = $assetProvider->getUxCssCollection();
            if ($collection) {
                $scripts->add($collection);
            }
        }

        foreach ($this->assetProviders->getAssetProviders() as $assetProvider) {
            $collection = $assetProvider->getCssCollection();
            if ($collection) {
                $scripts->add($collection);
            }
        }

        $cache = new AssetCache(
            $scripts,
            new FilesystemCache($this->cacheDir)
        );

        return $cache->dump();
    }
}
