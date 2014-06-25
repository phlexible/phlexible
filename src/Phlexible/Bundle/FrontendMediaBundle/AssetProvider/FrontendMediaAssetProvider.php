<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Frontend media asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FrontendMediaAssetProvider implements AssetProviderInterface
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
            new FileAsset($this->locator->locate('@PhlexibleFrontendMediaBundle/Resources/scripts/Definitions.js')),
            new FileAsset($this->locator->locate('@PhlexibleFrontendMediaBundle/Resources/scripts/MediaTemplatesWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleFrontendMediaBundle/Resources/scripts/FieldHelper.js')),
            new FileAsset($this->locator->locate('@PhlexibleFrontendMediaBundle/Resources/scripts/FolderSelector.js')),

            new FileAsset($this->locator->locate('@PhlexibleFrontendMediaBundle/Resources/scripts/fields/Folder.js')),
            new FileAsset($this->locator->locate('@PhlexibleFrontendMediaBundle/Resources/scripts/fields/DownloadFile.js')),
            new FileAsset($this->locator->locate('@PhlexibleFrontendMediaBundle/Resources/scripts/fields/ImageFile.js')),
            new FileAsset($this->locator->locate('@PhlexibleFrontendMediaBundle/Resources/scripts/fields/FlashFile.js')),
            new FileAsset($this->locator->locate('@PhlexibleFrontendMediaBundle/Resources/scripts/fields/VideoFile.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleFrontendMediaBundle/Resources/styles/folderselector.css')),
        ));

        return $collection;
    }
}
