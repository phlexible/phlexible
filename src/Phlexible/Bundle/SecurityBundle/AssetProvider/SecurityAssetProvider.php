<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Security asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SecurityAssetProvider implements AssetProviderInterface
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
            new FileAsset($this->locator->locate('@PhlexibleSecurityBundle/Resources/scripts/Definitions.js')),

            new FileAsset($this->locator->locate('@PhlexibleSecurityBundle/Resources/scripts/model/Resource.js')),
            new FileAsset($this->locator->locate('@PhlexibleSecurityBundle/Resources/scripts/model/Role.js')),
            new FileAsset($this->locator->locate('@PhlexibleSecurityBundle/Resources/scripts/model/RoleResource.js')),

            new FileAsset($this->locator->locate('@PhlexibleSecurityBundle/Resources/scripts/MainPanel.js')),

            new FileAsset($this->locator->locate('@PhlexibleSecurityBundle/Resources/scripts/menuhandle/LogoutHandle.js')),
            new FileAsset($this->locator->locate('@PhlexibleSecurityBundle/Resources/scripts/menuhandle/RolesHandle.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleSecurityBundle/Resources/styles/auth.css')),
        ));

        return $collection;
    }
}
