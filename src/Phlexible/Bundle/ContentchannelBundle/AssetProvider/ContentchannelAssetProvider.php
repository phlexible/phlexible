<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ContentchannelBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Contentchannel asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentchannelAssetProvider implements AssetProviderInterface
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
        return [
            '@PhlexibleContentchannelBundle/Resources/scripts/Definitions.js',
            '@PhlexibleContentchannelBundle/Resources/scripts/MainPanel.js',
            '@PhlexibleContentchannelBundle/Resources/scripts/ContentchannelsGrid.js',
            '@PhlexibleContentchannelBundle/Resources/scripts/ContentchannelForm.js',
            '@PhlexibleContentchannelBundle/Resources/scripts/menuhandle/ContentchannelsHandle.js',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return null;
    }
}
