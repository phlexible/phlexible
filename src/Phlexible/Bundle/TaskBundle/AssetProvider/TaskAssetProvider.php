<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TaskBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Task asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TaskAssetProvider implements AssetProviderInterface
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
            '@PhlexibleTaskBundle/Resources/scripts/Definitions.js',

            '@PhlexibleTaskBundle/Resources/scripts/Definitions.js',
            '@PhlexibleTaskBundle/Resources/scripts/MainPanel.js',
            '@PhlexibleTaskBundle/Resources/scripts/FilterPanel.js',
            '@PhlexibleTaskBundle/Resources/scripts/TasksGrid.js',
            '@PhlexibleTaskBundle/Resources/scripts/TaskManager.js',

            '@PhlexibleTaskBundle/Resources/scripts/window/NewTaskWindow.js',
            '@PhlexibleTaskBundle/Resources/scripts/window/AssignWindow.js',
            '@PhlexibleTaskBundle/Resources/scripts/window/CommentWindow.js',

            '@PhlexibleTaskBundle/Resources/scripts/portlet/MyTasks.js',

            '@PhlexibleTaskBundle/Resources/scripts/menuhandle/TasksHandle.js',

            '@PhlexibleTaskBundle/Resources/scripts/model/Task.js',
            '@PhlexibleTaskBundle/Resources/scripts/model/Comment.js',
            '@PhlexibleTaskBundle/Resources/scripts/model/Transition.js',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return [
            '@PhlexibleTaskBundle/Resources/styles/tasks.css',
        ];
    }
}
