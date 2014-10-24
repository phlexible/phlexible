<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Meta set asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetAssetProvider implements AssetProviderInterface
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
            '@PhlexibleMetaSetBundle/Resources/scripts/Definitions.js',

            '@PhlexibleMetaSetBundle/Resources/scripts/Fields.js',
            '@PhlexibleMetaSetBundle/Resources/scripts/MainPanel.js',
            '@PhlexibleMetaSetBundle/Resources/scripts/MetaSetsWindow.js',
            '@PhlexibleMetaSetBundle/Resources/scripts/MetaSuggestWindow.js',

            '@PhlexibleMetaSetBundle/Resources/scripts/SelectConfigurationWindow.js',
            '@PhlexibleMetaSetBundle/Resources/scripts/SuggestConfigurationWindow.js',

            '@PhlexibleMetaSetBundle/Resources/scripts/menuhandle/MetaSetsHandle.js',
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
