<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Media templates asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaTemplatesAssetProvider implements AssetProviderInterface
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
            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/Definitions.js')),

            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/MainPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/BasePreviewPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/TemplatesGrid.js')),

            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/image/FormPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/image/PreviewPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/image/MainPanel.js')),

            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/video/FormPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/video/PreviewPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/video/MainPanel.js')),

            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/audio/FormPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/audio/PreviewPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/audio/MainPanel.js')),

            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/pdf2swf/FormPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/pdf2swf/PreviewPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/pdf2swf/MainPanel.js')),

            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/scripts/menuhandle/MediaTemplatesHandle.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleMediaTemplateBundle/Resources/styles/mediatemplates.css')),
        ));

        return $collection;
    }
}
