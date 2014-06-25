<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Task asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TaskAssetProvider implements AssetProviderInterface
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
            new FileAsset($this->locator->locate('@PhlexibleTaskBundle/Resources/scripts/Definitions.js')),

            new FileAsset($this->locator->locate('@PhlexibleTaskBundle/Resources/scripts/Definitions.js')),
            new FileAsset($this->locator->locate('@PhlexibleTaskBundle/Resources/scripts/MainPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleTaskBundle/Resources/scripts/FilterPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleTaskBundle/Resources/scripts/TasksGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleTaskBundle/Resources/scripts/NewTaskWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleTaskBundle/Resources/scripts/ViewWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleTaskBundle/Resources/scripts/Manager.js')),

            new FileAsset($this->locator->locate('@PhlexibleTaskBundle/Resources/scripts/portlet/MyTasks.js')),

            new FileAsset($this->locator->locate('@PhlexibleTaskBundle/Resources/scripts/menuhandle/TasksHandle.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleTaskBundle/Resources/styles/tasks.css')),
        ));

        return $collection;
    }
}
