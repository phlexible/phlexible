<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Frontend media asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FrontendMediaAssetProvider implements AssetProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getUxScriptsCollection()
    {
        return [
            '@PhlexibleFrontendMediaBundle/Resources/scripts/ux/Ext.ux.form.FileField.js',
            '@PhlexibleFrontendMediaBundle/Resources/scripts/ux/Ext.ux.form.FolderField.js',
        ];
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
        return [
            '@PhlexibleFrontendMediaBundle/Resources/scripts/Definitions.js',
            '@PhlexibleFrontendMediaBundle/Resources/scripts/FieldHelper.js',

            '@PhlexibleFrontendMediaBundle/Resources/scripts/fields/Folder.js',
            '@PhlexibleFrontendMediaBundle/Resources/scripts/fields/File.js',

            '@PhlexibleFrontendMediaBundle/Resources/scripts/configuration/FieldConfigurationFile.js',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return [
            '@PhlexibleFrontendMediaBundle/Resources/styles/folderselector.css',
        ];
    }
}
