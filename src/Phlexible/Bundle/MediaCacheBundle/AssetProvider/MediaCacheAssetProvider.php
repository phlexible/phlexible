<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Media cache asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaCacheAssetProvider implements AssetProviderInterface
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
            '@PhlexibleMediaCacheBundle/Resources/scripts/portlet/CacheStatus.js'
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
