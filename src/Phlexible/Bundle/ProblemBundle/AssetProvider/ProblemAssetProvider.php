<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ProblemBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Problem asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ProblemAssetProvider implements AssetProviderInterface
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
            '@PhlexibleProblemBundle/Resources/scripts/Definitions.js',

            '@PhlexibleProblemBundle/Resources/scripts/ProblemsGrid.js',

            '@PhlexibleProblemBundle/Resources/scripts/model/Problem.js',

            '@PhlexibleProblemBundle/Resources/scripts/portlet/Problems.js',

            '@PhlexibleProblemBundle/Resources/scripts/menuhandle/ProblemsHandle.js',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return [
            '@PhlexibleProblemBundle/Resources/styles/problems.css',
        ];
    }
}
