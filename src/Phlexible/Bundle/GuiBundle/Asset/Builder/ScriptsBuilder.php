<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Builder;

use Assetic\Asset\AssetCache;
use Assetic\Cache\FilesystemCache;
use Assetic\FilterManager;
use Phlexible\Bundle\GuiBundle\Asset\Filter\FilenameFilter;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderCollection;
use Phlexible\Bundle\GuiBundle\Compressor\JavascriptCompressor\JavascriptCompressorInterface;
use Symfony\Bundle\AsseticBundle\Factory\AssetFactory;

/**
 * Scripts builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ScriptsBuilder
{
    /**
     * @var AssetFactory
     */
    private $assetFactory;

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
     * @param AssetFactory                  $assetFactory
     * @param AssetProviderCollection       $assetProviders
     * @param JavascriptCompressorInterface $javascriptCompressor
     * @param string                        $cacheDir
     * @param bool                          $debug
     */
    public function __construct(
        AssetFactory $assetFactory,
        AssetProviderCollection $assetProviders,
        JavascriptCompressorInterface $javascriptCompressor,
        $cacheDir,
        $debug)
    {
        $this->assetFactory = $assetFactory;
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
        $fm = new FilterManager();
        $fm->set('compressor', $this->javascriptCompressor);
        $fm->set('filename', new FilenameFilter());

        $filters = [
            'filename',
        ];

        if (!$this->debug) {
            $filters[] = 'compressor';
            //$filters[] = new Assetic\Filter\Yui\JsCompressorFilter('/Users/swentz/Sites/ofcs/hoffmann/app/Resources/java/yuicompressor-2.4.7.jar');
            //$filters[] = new Assetic\Filter\JsMinFilter();
        }

        $input = [];

        foreach ($this->assetProviders->getAssetProviders() as $assetProvider) {
            $collection = $assetProvider->getUxScriptsCollection();
            if ($collection === null) {
                continue;
            }
            if (!is_array($collection)) {
                throw new \InvalidArgumentException('Collection needs to be an array.');
            }
            $input = array_merge($input, $collection);
        }

        foreach ($this->assetProviders->getAssetProviders() as $assetProvider) {
            $collection = $assetProvider->getScriptsCollection();
            if ($collection === null) {
                continue;
            }
            if (!is_array($collection)) {
                throw new \InvalidArgumentException('Collection needs to be an array.');
            }
            $input = array_merge($input, $collection);
        }

        $this->assetFactory->setFilterManager($fm);
        $asset = $this->assetFactory->createAsset($input, $filters);

        $cache = new AssetCache(
            $asset,
            new FilesystemCache($this->cacheDir)
        );

        return $cache->dump();
    }
}
