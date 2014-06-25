<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\GuiBundle\AssetProvider;

use Assetic\Asset\AssetCollection;

/**
 * Asset provider collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AssetProviderCollection implements AssetProviderInterface
{
    /**
     * @var AssetProviderInterface[]
     */
    private $assetProviders = array();

    /**
     * @param array $assetProviders
     */
    public function __construct(array $assetProviders = array())
    {
        foreach ($assetProviders as $assetProvider) {
            $this->addAssetProvider($assetProvider);
        }
    }

    /**
     * Add asset provider
     *
     * @param AssetProviderInterface $assetProvider
     *
     * @return AssetProviderCollection
     */
    public function addAssetProvider(AssetProviderInterface $assetProvider)
    {
        $this->assetProviders[] = $assetProvider;

        return $this;
    }

    /**
     * Return asset providers
     *
     * @return AssetProviderInterface[]
     */
    public function getAssetProviders()
    {
        return $this->assetProviders;
    }

    /**
     * @return AssetCollection
     */
    public function getUxScriptsCollection()
    {
        $scripts = new AssetCollection();

        foreach ($this->getAssetProviders() as $assetProvider) {
            $collection = $assetProvider->getUxScriptsCollection();
            if ($collection) {
                $scripts->add($collection);
            }
        }

        return $scripts;
    }

    /**
     * @return AssetCollection
     */
    public function getUxCssCollection()
    {
        $css = new AssetCollection();

        foreach ($this->getAssetProviders() as $assetProvider) {
            $collection = $assetProvider->getUxCssCollection();
            if ($collection) {
                $css->add($collection);
            }
        }

        return $css;

    }

    /**
     * @return AssetCollection
     */
    public function getScriptsCollection()
    {
        $scripts = new AssetCollection();

        foreach ($this->getAssetProviders() as $assetProvider) {
            $collection = $assetProvider->getScriptsCollection();
            if ($collection) {
                $scripts->add($collection);
            }
        }

        return $scripts;

    }

    /**
     * @return AssetCollection
     */
    public function getCssCollection()
    {
        $css = new AssetCollection();

        foreach ($this->getAssetProviders() as $assetProvider) {
            $collection = $assetProvider->getCssCollection();
            if ($collection) {
                $css->add($collection);
            }
        }

        return $css;
    }
}
