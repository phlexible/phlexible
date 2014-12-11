<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DashboardBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Dashboard asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DashboardAssetProvider implements AssetProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getUxScriptsCollection()
    {
        return [
            '@PhlexibleDashboardBundle/Resources/scripts-ux/Ext.ux.Portal.js',
            '@PhlexibleDashboardBundle/Resources/scripts-ux/Ext.ux.PortalColumn.js',
            '@PhlexibleDashboardBundle/Resources/scripts-ux/Ext.ux.Portlet.js',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getUxCssCollection()
    {
        return [
            '@PhlexibleDashboardBundle/Resources/styles/ux/Ext.ux.Portlet.css',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getScriptsCollection()
    {
        return [
            '@PhlexibleDashboardBundle/Resources/scripts/Definitions.js',
            //'@PhlexibleDashboardBundle/Resources/scripts/StartMessage.js',
            '@PhlexibleDashboardBundle/Resources/scripts/ListPanel.js',
            '@PhlexibleDashboardBundle/Resources/scripts/PortalPanel.js',
            '@PhlexibleDashboardBundle/Resources/scripts/MainPanel.js',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return [
            '@PhlexibleDashboardBundle/Resources/styles/portlets.css',
            '@PhlexibleDashboardBundle/Resources/styles/dashboard.css',
        ];
    }
}
