<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CacheBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Cache asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CacheAssetProvider implements AssetProviderInterface
{
    /**
     * @var FileLocator
     */
    private $locator;

    /**
     * @param FileLocator $locator
     */
    public function __construct(FileLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * {@inheritDoc}
     */
    public function getUxScriptsCollection()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getUxCssCollection()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getScriptsCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleCacheBundle/Resources/scripts/Definitions.js')),
            new FileAsset($this->locator->locate('@PhlexibleCacheBundle/Resources/scripts/CacheStatsWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleCacheBundle/Resources/scripts/portlet/CacheUsage.js')),
            new FileAsset($this->locator->locate(
                '@PhlexibleCacheBundle/Resources/scripts/menuhandle/CacheStatsHandle.js'
            )),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleCacheBundle/Resources/styles/portlet.css')),
        ));

        return $collection;
    }
}
