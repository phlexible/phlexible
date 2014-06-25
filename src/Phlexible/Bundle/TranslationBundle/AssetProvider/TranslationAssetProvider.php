<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TranslationBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Translation asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TranslationAssetProvider implements AssetProviderInterface
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
            new FileAsset($this->locator->locate('@PhlexibleTranslationBundle/Resources/scripts/Definitions.js')),

            new FileAsset($this->locator->locate('@PhlexibleTranslationBundle/Resources/scripts/TranslationFilterPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleTranslationBundle/Resources/scripts/TranslationGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleTranslationBundle/Resources/scripts/MainPanel.js')),

            new FileAsset($this->locator->locate('@PhlexibleTranslationBundle/Resources/scripts/menuhandle/TranslationsHandle.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleTranslationBundle/Resources/styles/translations.css')),
        ));

        return $collection;
    }
}
