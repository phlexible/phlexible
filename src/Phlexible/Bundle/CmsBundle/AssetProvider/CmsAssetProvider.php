<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Cms asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CmsAssetProvider implements AssetProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getUxScriptsCollection()
    {
        return array(
            '@PhlexibleCmsBundle/Resources/scripts/ux/Ext.form.FileUploadField.js',
            '@PhlexibleCmsBundle/Resources/scripts/ux/Ext.ux.DDView.js',
            '@PhlexibleCmsBundle/Resources/scripts/ux/Ext.ux.Multiselect.js',
            '@PhlexibleCmsBundle/Resources/scripts/ux/Ext.ux.TreeSelector.js',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getUxCssCollection()
    {
        return array(
            '@PhlexibleCmsBundle/Resources/styles/ux/Ext.form.FileUploadField.css',
            '@PhlexibleCmsBundle/Resources/styles/ux/Ext.ux.Multiselect.css',
            '@PhlexibleCmsBundle/Resources/styles/ux/Ext.ux.TreeSelector.css',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getScriptsCollection()
    {
        return array(
            '@PhlexibleCmsBundle/Resources/scripts/Definitions.js',

            '@PhlexibleCmsBundle/Resources/scripts/menuhandle/ReportsMenu.js',
            '@PhlexibleCmsBundle/Resources/scripts/menuhandle/StatisticsMenu.js',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return null;
    }
}
