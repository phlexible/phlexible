<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Siteroot asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootAssetProvider implements AssetProviderInterface
{
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
        return array(
            '@PhlexibleSiterootBundle/Resources/scripts/Definitions.js',

            '@PhlexibleSiterootBundle/Resources/scripts/model/Siteroot.js',
            '@PhlexibleSiterootBundle/Resources/scripts/model/Contentchannel.js',
            '@PhlexibleSiterootBundle/Resources/scripts/model/Navigation.js',
            '@PhlexibleSiterootBundle/Resources/scripts/model/SpecialTid.js',
            '@PhlexibleSiterootBundle/Resources/scripts/model/Url.js',

            '@PhlexibleSiterootBundle/Resources/scripts/SingleCheckColumn.js',
            '@PhlexibleSiterootBundle/Resources/scripts/LanguageCheckColumn.js',
            '@PhlexibleSiterootBundle/Resources/scripts/MainPanel.js',
            '@PhlexibleSiterootBundle/Resources/scripts/SiterootGrid.js',
            '@PhlexibleSiterootBundle/Resources/scripts/UrlGrid.js',
            '@PhlexibleSiterootBundle/Resources/scripts/SiterootNavigationWindow.js',
            '@PhlexibleSiterootBundle/Resources/scripts/NavigationFlagsWindow.js',
            '@PhlexibleSiterootBundle/Resources/scripts/NavigationGrid.js',
            '@PhlexibleSiterootBundle/Resources/scripts/SpecialTidGrid.js',
            '@PhlexibleSiterootBundle/Resources/scripts/ContentChannelGrid.js',
            '@PhlexibleSiterootBundle/Resources/scripts/TitleForm.js',
            '@PhlexibleSiterootBundle/Resources/scripts/PropertyGrid.js',
            '@PhlexibleSiterootBundle/Resources/scripts/menuhandle/SiterootsHandle.js',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return array(
            '@PhlexibleSiterootBundle/Resources/styles/siteroots.css',
        );
    }
}
