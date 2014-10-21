<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * User asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserAssetProvider implements AssetProviderInterface
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
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/Definitions.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/model/Group.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/model/User.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/model/UserGroup.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/model/UserRole.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/options/Details.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/options/Password.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/options/Preferences.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/options/Theme.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/portlet/Online.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/OptionsWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/UserGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/UserFilterPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/UserWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/UsersMainPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/GroupsMainPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/MainPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/SuccessorWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/menuhandle/UsersHandle.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/menuhandle/OptionsHandle.js')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/scripts/menuhandle/LogoutHandle.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/styles/users.css')),
            new FileAsset($this->locator->locate('@PhlexibleUserBundle/Resources/styles/portlet.css')),
        ));

        return $collection;
    }
}
