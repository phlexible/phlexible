<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Siteroot asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootAssetProvider implements AssetProviderInterface
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
            new FileAsset($this->locator->locate('@PhlexibleSiterootBundle/Resources/scripts/Definitions.js')),
            new FileAsset($this->locator->locate('@PhlexibleSiterootBundle/Resources/scripts/SingleCheckColumn.js')),
            new FileAsset($this->locator->locate('@PhlexibleSiterootBundle/Resources/scripts/LanguageCheckColumn.js')),
            new FileAsset($this->locator->locate('@PhlexibleSiterootBundle/Resources/scripts/MainPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleSiterootBundle/Resources/scripts/SiterootGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleSiterootBundle/Resources/scripts/UrlGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleSiterootBundle/Resources/scripts/ShortUrlGrid.js')),
            new FileAsset($this->locator->locate(
                '@PhlexibleSiterootBundle/Resources/scripts/SiterootNavigationWindow.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleSiterootBundle/Resources/scripts/NavigationFlagsWindow.js'
            )),
            new FileAsset($this->locator->locate('@PhlexibleSiterootBundle/Resources/scripts/NavigationGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleSiterootBundle/Resources/scripts/SpecialTidGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleSiterootBundle/Resources/scripts/ContentChannelGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleSiterootBundle/Resources/scripts/TitleForm.js')),
            new FileAsset($this->locator->locate('@PhlexibleSiterootBundle/Resources/scripts/CustomTitleForm.js')),
            new FileAsset($this->locator->locate('@PhlexibleSiterootBundle/Resources/scripts/PropertyGrid.js')),
            new FileAsset($this->locator->locate(
                '@PhlexibleSiterootBundle/Resources/scripts/menuhandle/SiterootsHandle.js'
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
            new FileAsset($this->locator->locate('@PhlexibleSiterootBundle/Resources/styles/siteroots.css')),
        ));

        return $collection;
    }
}
