<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Meta set asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetAssetProvider implements AssetProviderInterface
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
            new FileAsset($this->locator->locate('@PhlexibleMetaSetBundle/Resources/scripts/Definitions.js')),

            new FileAsset($this->locator->locate('@PhlexibleMetaSetBundle/Resources/scripts/Fields.js')),
            new FileAsset($this->locator->locate('@PhlexibleMetaSetBundle/Resources/scripts/MainPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMetaSetBundle/Resources/scripts/MetaSetsWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleMetaSetBundle/Resources/scripts/MetaSuggestWindow.js')),

            new FileAsset($this->locator->locate('@PhlexibleMetaSetBundle/Resources/scripts/menuhandle/MetaSetsHandle.js')),
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
