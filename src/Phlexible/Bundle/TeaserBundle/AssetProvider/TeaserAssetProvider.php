<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Teaser asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserAssetProvider implements AssetProviderInterface
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
            '@PhlexibleTeaserBundle/Resources/scripts/Definitions.js',

            '@PhlexibleTeaserBundle/Resources/scripts/ElementLayoutTree.js',
            '@PhlexibleTeaserBundle/Resources/scripts/ElementLayoutTreeLoader.js',
            '@PhlexibleTeaserBundle/Resources/scripts/ElementLayoutTreeNodeUI.js',
            '@PhlexibleTeaserBundle/Resources/scripts/ElementLayoutPanel.js',
            '@PhlexibleTeaserBundle/Resources/scripts/NewTeaserWindow.js',
            '@PhlexibleTeaserBundle/Resources/scripts/NewTeaserInstanceWindow.js',
            '@PhlexibleTeaserBundle/Resources/scripts/PublishTeaserWindow.js',
            '@PhlexibleTeaserBundle/Resources/scripts/SetTeaserOfflineWindow.js',
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
