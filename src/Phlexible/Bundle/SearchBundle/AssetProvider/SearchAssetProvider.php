<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SearchBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Search asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SearchAssetProvider implements AssetProviderInterface
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
            '@PhlexibleSearchBundle/Resources/scripts/Definitions.js',

            '@PhlexibleSearchBundle/Resources/scripts/SearchBox.js',
            '@PhlexibleSearchBundle/Resources/scripts/SearchPanel.js',

            '@PhlexibleSearchBundle/Resources/scripts/menuhandle/SearchBoxHandle.js',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return array(
            '@PhlexibleSearchBundle/Resources/styles/search.css',
        );
    }
}
