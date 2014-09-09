<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Element finder asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderAssetProvider implements AssetProviderInterface
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
        return new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleElementFinderBundle/Resources/scripts/ux/Ext.ux.form.FinderField.js')),
        ));
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
            new FileAsset($this->locator->locate('@PhlexibleElementFinderBundle/Resources/scripts/Definitions.js')),

            new FileAsset($this->locator->locate('@PhlexibleElementFinderBundle/Resources/scripts/ElementFinderConfigWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementFinderBundle/Resources/scripts/ElementFinderConfigPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementFinderBundle/Resources/scripts/NewCatchWindow.js')),

            new FileAsset($this->locator->locate('@PhlexibleElementFinderBundle/Resources/scripts/configuration/FieldConfigurationFinder.js')),

            new FileAsset($this->locator->locate('@PhlexibleElementFinderBundle/Resources/scripts/field/Finder.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return null;
    }
}
