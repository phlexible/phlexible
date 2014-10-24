<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Message asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageAssetProvider implements AssetProviderInterface
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
            '@PhlexibleMessageBundle/Resources/scripts/Definitions.js',

            '@PhlexibleMessageBundle/Resources/scripts/model/Criterium.js',
            '@PhlexibleMessageBundle/Resources/scripts/model/Filter.js',
            '@PhlexibleMessageBundle/Resources/scripts/model/Message.js',
            '@PhlexibleMessageBundle/Resources/scripts/model/Subscription.js',

            '@PhlexibleMessageBundle/Resources/scripts/view/MainPanel.js',
            '@PhlexibleMessageBundle/Resources/scripts/view/FilterPanel.js',
            '@PhlexibleMessageBundle/Resources/scripts/view/MessagesGrid.js',

            '@PhlexibleMessageBundle/Resources/scripts/filter/MainPanel.js',
            '@PhlexibleMessageBundle/Resources/scripts/filter/ListGrid.js',
            '@PhlexibleMessageBundle/Resources/scripts/filter/CriteriaForm.js',
            '@PhlexibleMessageBundle/Resources/scripts/filter/PreviewPanel.js',

            '@PhlexibleMessageBundle/Resources/scripts/subscription/MainPanel.js',

            '@PhlexibleMessageBundle/Resources/scripts/MainPanel.js',

            '@PhlexibleMessageBundle/Resources/scripts/portlet/Messages.js',

            '@PhlexibleMessageBundle/Resources/scripts/menuhandle/MessagesHandle.js',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return array(
            '@PhlexibleMessageBundle/Resources/styles/messages.css',
            '@PhlexibleMessageBundle/Resources/styles/filter.css',
            '@PhlexibleMessageBundle/Resources/styles/portlet.css',
        );
    }
}
