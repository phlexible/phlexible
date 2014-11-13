<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Access control asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AccessControlAssetProvider implements AssetProviderInterface
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
            '@PhlexibleAccessControlBundle/Resources/scripts/Definitions.js',

            '@PhlexibleAccessControlBundle/Resources/scripts/RightsGrid.js',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return [
            '@PhlexibleAccessControlBundle/Resources/styles/actions.css',
        ];
    }
}
