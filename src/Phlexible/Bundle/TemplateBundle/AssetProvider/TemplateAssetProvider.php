<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TemplateBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Template asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TemplateAssetProvider implements AssetProviderInterface
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
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/scripts/syntaxhighlighter/shCore.js')),
            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/scripts/syntaxhighlighter/shBrushXml.js')),
            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/scripts/syntaxhighlighter/shBrushDwoo.js')),
            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/scripts/syntaxhighlighter/shBrushCss.js')),
            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/scripts/syntaxhighlighter/shBrushJScript.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getUxCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/styles/syntaxhighlighter/shCore.css')),
            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/styles/syntaxhighlighter/shThemeDefault.css')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getScriptsCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/scripts/Definitions.js')),

            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/scripts/TemplatesGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/scripts/TemplateTabPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/scripts/DataPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/scripts/ViewPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/scripts/EditorPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/scripts/MainPanel.js')),

            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/scripts/menuhandle/TemplatesHandle.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleTemplateBundle/Resources/styles/templates.css')),
        ));

        return $collection;
    }
}
