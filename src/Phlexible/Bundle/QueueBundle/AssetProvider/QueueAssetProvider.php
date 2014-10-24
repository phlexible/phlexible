<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\QueueBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Queue asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class QueueAssetProvider implements AssetProviderInterface
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
            '@PhlexibleQueueBundle/Resources/scripts/Definitions.js',

            '@PhlexibleQueueBundle/Resources/scripts/QueueStatsWindow.js',

            '@PhlexibleQueueBundle/Resources/scripts/model/Job.js',

            '@PhlexibleQueueBundle/Resources/scripts/menuhandle/QueueStatsHandle.js',
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
