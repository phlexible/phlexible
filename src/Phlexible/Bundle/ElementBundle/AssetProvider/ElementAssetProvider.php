<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Element asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementAssetProvider implements AssetProviderInterface
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
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ux/Ext.ux.layout.RowFitLayout.js')),
        ));

        return $collection;
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
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/Definitions.js')),

            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/menuhandle/ElementsHandle.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/menuhandle/ElementHandle.js')),

            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/Clipboard.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/Element.js')),

            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/model/ElementHistory.js')),

            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementsTreeDropZone.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementsTreeNodeUI.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementsTreeLoader.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementsTree.js')),

            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/accordions/AllowedChildrenAccordion.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/accordions/CommentAccordion.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/accordions/ConfigurationAccordion.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/accordions/ContextAccordion.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/accordions/DataAccordion.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/accordions/DiffAccordion.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/accordions/InstancesAccordion.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/accordions/MetaAccordion.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/accordions/QuickInfo.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/accordions/VersionsAccordion.js')),

            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/HistoryWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/HistoryFilter.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/HistoryGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/RightsGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementAccordion.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementListGridFilter.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementListGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementDataPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementContentPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementContentTabPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementTabPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementDataTabHelper.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementDataTab.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementLinksGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementHistoryGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/NewElementWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/NewElementInstanceWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/PublishTreeNodeWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/SetTreeNodeOfflineWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/ElementDataWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/EidSelector.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/LinkWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/TaskBar.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/PublishSlaveWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/DeleteInstancesWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/MainPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/TopToolbar.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/FileLinkWindow.js')),

            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/scripts/portlet/LatestElements.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/styles/elements.css')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/styles/portlet.css')),
            new FileAsset($this->locator->locate('@PhlexibleElementBundle/Resources/styles/eidselector.css')),
        ));

        return $collection;
    }
}
