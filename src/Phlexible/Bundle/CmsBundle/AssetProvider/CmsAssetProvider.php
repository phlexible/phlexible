<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Cms asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CmsAssetProvider implements AssetProviderInterface
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
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleCmsBundle/Resources/scripts/ux/Ext.form.FileUploadField.js')),
            new FileAsset($this->locator->locate('@PhlexibleCmsBundle/Resources/scripts/ux/Ext.ux.TwinComboBox.js')),
            new FileAsset($this->locator->locate('@PhlexibleCmsBundle/Resources/scripts/ux/Ext.ux.DDView.js')),
            new FileAsset($this->locator->locate('@PhlexibleCmsBundle/Resources/scripts/ux/Ext.ux.Multiselect.js')),
            new FileAsset($this->locator->locate('@PhlexibleCmsBundle/Resources/scripts/ux/Ext.ux.TreeSelector.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getUxCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleCmsBundle/Resources/styles/ux/Ext.form.FileUploadField.css')),
            new FileAsset($this->locator->locate('@PhlexibleCmsBundle/Resources/styles/ux/Ext.ux.Multiselect.css')),
            new FileAsset($this->locator->locate('@PhlexibleCmsBundle/Resources/styles/ux/Ext.ux.TreeSelector.css')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getScriptsCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleCmsBundle/Resources/scripts/Definitions.js')),

            new FileAsset($this->locator->locate('@PhlexibleCmsBundle/Resources/scripts/menuhandle/ReportsMenu.js')),
            new FileAsset($this->locator->locate('@PhlexibleCmsBundle/Resources/scripts/menuhandle/StatisticsMenu.js')),
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
