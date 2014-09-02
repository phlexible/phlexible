<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Teaser asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserAssetProvider implements AssetProviderInterface
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
            new FileAsset($this->locator->locate('@PhlexibleTeaserBundle/Resources/scripts/Definitions.js')),

            new FileAsset($this->locator->locate('@PhlexibleTeaserBundle/Resources/scripts/CatchDataPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleTeaserBundle/Resources/scripts/ElementLayoutTree.js')),
            new FileAsset($this->locator->locate('@PhlexibleTeaserBundle/Resources/scripts/ElementLayoutPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleTeaserBundle/Resources/scripts/NewTeaserWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleTeaserBundle/Resources/scripts/NewTeaserInstanceWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleTeaserBundle/Resources/scripts/NewCatchWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleTeaserBundle/Resources/scripts/PublishTeaserWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleTeaserBundle/Resources/scripts/SetTeaserOfflineWindow.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return null;
    }
}
