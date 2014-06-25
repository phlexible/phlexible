<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ProblemBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Problem asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ProblemAssetProvider implements AssetProviderInterface
{
    /**
     * @var FileLocator
     */
    private $locator;

    /**
     * @param FileLocator $locator
     */
    public function __construct(FileLocator $locator)
    {
        $this->locator = $locator;
    }

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
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleProblemBundle/Resources/scripts/Definitions.js')),

            new FileAsset($this->locator->locate('@PhlexibleProblemBundle/Resources/scripts/Problemsgrid.js')),

            new FileAsset($this->locator->locate('@PhlexibleProblemBundle/Resources/scripts/model/Problem.js')),

            new FileAsset($this->locator->locate('@PhlexibleProblemBundle/Resources/scripts/portlet/Problems.js')),

            new FileAsset($this->locator->locate('@PhlexibleProblemBundle/Resources/scripts/menuhandle/ProblemsHandle.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleProblemBundle/Resources/styles/problems.css')),
        ));

        return $collection;
    }
}
