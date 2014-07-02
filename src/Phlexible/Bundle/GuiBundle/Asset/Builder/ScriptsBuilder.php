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
use Phlexible\Bundle\GuiBundle\Asset\Filter\FilenameFilter;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderCollection;
use Phlexible\Bundle\GuiBundle\Compressor\JavascriptCompressor\JavascriptCompressorInterface;

/**
 * Scripts builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ScriptsBuilder
{
    /**
     * @var AssetProviderCollection
     */
    private $assetProviders;

    /**
     * @var JavascriptCompressorInterface
     */
    private $javascriptCompressor;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param AssetProviderCollection       $assetProviders
     * @param JavascriptCompressorInterface $javascriptCompressor
     * @param string                        $cacheDir
     * @param bool                          $debug
     */
    public function __construct(AssetProviderCollection $assetProviders,
                                JavascriptCompressorInterface $javascriptCompressor,
                                $cacheDir,
                                $debug)
    {
        $this->assetProviders = $assetProviders;
        $this->javascriptCompressor = $javascriptCompressor;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    /**
     * Get all javascripts for the given section
     *
     * @return string
     */
    public function get()
    {
        $filters = array();
        if (!$this->debug) {
            $filters[] = $this->javascriptCompressor;
            $filters[] = new FilenameFilter();
            //$filters[] = new Assetic\Filter\Yui\JsCompressorFilter('/Users/swentz/Sites/ofcs/hoffmann/app/Resources/java/yuicompressor-2.4.7.jar');
            //$filters[] = new Assetic\Filter\JsMinFilter();
        }

        $scripts = new AssetCollection(array(), $filters);

        foreach ($this->assetProviders->getAssetProviders() as $assetProvider) {
            $collection = $assetProvider->getUxScriptsCollection();
            if ($collection) {
                $scripts->add($collection);
            }
        }

        foreach ($this->assetProviders->getAssetProviders() as $assetProvider) {
            $collection = $assetProvider->getScriptsCollection();
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
