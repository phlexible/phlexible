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
use Assetic\Asset\FileAsset;
use Assetic\Cache\FilesystemCache;
use Assetic\FilterManager;
use Phlexible\Bundle\GuiBundle\Asset\Filter\BaseUrlFilter;
use Phlexible\Bundle\GuiBundle\Asset\Filter\FilenameFilter;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderCollection;
use Phlexible\Bundle\GuiBundle\Compressor\CssCompressor\CssCompressorInterface;
use Puli\Repository\FilesystemRepository;
use Puli\Repository\Resource\FileResource;
use Puli\Repository\ResourceRepositoryInterface;
use Symfony\Bundle\AsseticBundle\Factory\AssetFactory;

/**
 * CSS builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CssBuilder
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
     * @param AssetFactory            $assetFactory
     * @param AssetProviderCollection $assetProviders
     * @param CssCompressorInterface  $cssCompressor
     * @param string                  $cacheDir
     * @param bool                    $debug
     */
    public function __construct(
        AssetFactory $assetFactory,
        AssetProviderCollection $assetProviders,
        CssCompressorInterface $cssCompressor,
        $cacheDir,
        $debug)
    {
        $this->assetFactory = $assetFactory;
        $this->assetProviders = $assetProviders;
        $this->cssCompressor = $cssCompressor;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    /**
     * Build stream
     *
     * @param string               $baseUrl
     * @param string               $basePath
     * @param FilesystemRepository $repo
     *
     * @return string
     */
    public function get($baseUrl, $basePath, FilesystemRepository $repo)
    {
        $fm = new FilterManager();
        $fm->set('baseurl', new BaseUrlFilter($baseUrl, $basePath));
        $fm->set('compressor', $this->cssCompressor);
        $fm->set('filename', new FilenameFilter());

        $filters = [
            'baseurl',
            'filename',
        ];

        if (!$this->debug) {
            $filters[] = 'compressor';
            //$filters[] = new Assetic\Filter\Yui\JsCompressorFilter('/Users/swentz/Sites/ofcs/hoffmann/app/Resources/java/yuicompressor-2.4.7.jar');
            //$filters[] = new Assetic\Filter\CssMinFilter();
        }

        $input = [];

        foreach ($repo->find('/phlexible/styles/*/*.css') as $resource) {
            /* @var $resource FileResource */
            $input[] = $resource->getFilesystemPath();
        }

        /*
        foreach ($this->assetProviders->getAssetProviders() as $assetProvider) {
            $collection = $assetProvider->getUxCssCollection();
            if ($collection === null) {
                continue;
            }
            if (!is_array($collection)) {
                throw new \InvalidArgumentException('Collection needs to be an array.');
            }
            $input = array_merge($input, $collection);
        }

        foreach ($this->assetProviders->getAssetProviders() as $assetProvider) {
            $collection = $assetProvider->getCssCollection();
            if ($collection === null) {
                continue;
            }
            if (!is_array($collection)) {
                throw new \InvalidArgumentException('Collection needs to be an array.');
            }
            $input = array_merge($input, $collection);
        }
        */

        $this->assetFactory->setFilterManager($fm);
        $asset = $this->assetFactory->createAsset($input, $filters);

        $cache = new AssetCache(
            $asset,
            new FilesystemCache($this->cacheDir)
        );

        return $cache->dump();
    }
}
