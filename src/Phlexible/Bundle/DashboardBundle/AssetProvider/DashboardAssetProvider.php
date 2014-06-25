<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DashboardBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Dashboard asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DashboardAssetProvider implements AssetProviderInterface
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
            new FileAsset($this->locator->locate('@PhlexibleDashboardBundle/Resources/scripts/ux/Ext.ux.Portal.js')),
            new FileAsset($this->locator->locate('@PhlexibleDashboardBundle/Resources/scripts/ux/Ext.ux.Portal.DropZone.js')),
            new FileAsset($this->locator->locate('@PhlexibleDashboardBundle/Resources/scripts/ux/Ext.ux.PortalColumn.js')),
            new FileAsset($this->locator->locate('@PhlexibleDashboardBundle/Resources/scripts/ux/Ext.ux.Portlet.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getUxCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleDashboardBundle/Resources/styles/ux/Ext.ux.Portlet.css')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getScriptsCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleDashboardBundle/Resources/scripts/Definitions.js')),
            //new FileAsset($this->locator->locate('@PhlexibleDashboardBundle/Resources/scripts/StartMessage.js')),
            new FileAsset($this->locator->locate('@PhlexibleDashboardBundle/Resources/scripts/ListPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleDashboardBundle/Resources/scripts/PortalPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleDashboardBundle/Resources/scripts/MainPanel.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleDashboardBundle/Resources/styles/portlets.css')),
            new FileAsset($this->locator->locate('@PhlexibleDashboardBundle/Resources/styles/dashboard.css')),
        ));

        return $collection;
    }
}
