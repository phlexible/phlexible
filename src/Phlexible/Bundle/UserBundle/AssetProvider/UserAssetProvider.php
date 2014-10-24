<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * User asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserAssetProvider implements AssetProviderInterface
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
            '@PhlexibleUserBundle/Resources/scripts/Definitions.js',
            '@PhlexibleUserBundle/Resources/scripts/model/Group.js',
            '@PhlexibleUserBundle/Resources/scripts/model/User.js',
            '@PhlexibleUserBundle/Resources/scripts/model/UserGroup.js',
            '@PhlexibleUserBundle/Resources/scripts/model/UserRole.js',
            '@PhlexibleUserBundle/Resources/scripts/options/Details.js',
            '@PhlexibleUserBundle/Resources/scripts/options/Password.js',
            '@PhlexibleUserBundle/Resources/scripts/options/Preferences.js',
            '@PhlexibleUserBundle/Resources/scripts/options/Theme.js',
            '@PhlexibleUserBundle/Resources/scripts/portlet/Online.js',
            '@PhlexibleUserBundle/Resources/scripts/OptionsWindow.js',
            '@PhlexibleUserBundle/Resources/scripts/UserGrid.js',
            '@PhlexibleUserBundle/Resources/scripts/UserFilterPanel.js',
            '@PhlexibleUserBundle/Resources/scripts/UserWindow.js',
            '@PhlexibleUserBundle/Resources/scripts/UsersMainPanel.js',
            '@PhlexibleUserBundle/Resources/scripts/GroupsMainPanel.js',
            '@PhlexibleUserBundle/Resources/scripts/MainPanel.js',
            '@PhlexibleUserBundle/Resources/scripts/SuccessorWindow.js',
            '@PhlexibleUserBundle/Resources/scripts/menuhandle/UsersHandle.js',
            '@PhlexibleUserBundle/Resources/scripts/menuhandle/OptionsHandle.js',
            '@PhlexibleUserBundle/Resources/scripts/menuhandle/LogoutHandle.js',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return array(
            '@PhlexibleUserBundle/Resources/styles/users.css',
            '@PhlexibleUserBundle/Resources/styles/portlet.css',
        );
    }
}
