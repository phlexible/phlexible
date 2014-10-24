<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Element asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementAssetProvider implements AssetProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getUxScriptsCollection()
    {
        return array(
            '@PhlexibleElementBundle/Resources/scripts/ux/Ext.ux.layout.RowFitLayout.js',
        );
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
            '@PhlexibleElementBundle/Resources/scripts/Definitions.js',

            '@PhlexibleElementBundle/Resources/scripts/menuhandle/ElementsHandle.js',
            '@PhlexibleElementBundle/Resources/scripts/menuhandle/ElementHandle.js',

            '@PhlexibleElementBundle/Resources/scripts/Clipboard.js',
            '@PhlexibleElementBundle/Resources/scripts/Element.js',

            '@PhlexibleElementBundle/Resources/scripts/model/ElementHistory.js',

            '@PhlexibleElementBundle/Resources/scripts/ElementsTreeDropZone.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementsTreeNodeUI.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementsTreeLoader.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementsTree.js',

            '@PhlexibleElementBundle/Resources/scripts/accordions/AllowedChildrenAccordion.js',
            '@PhlexibleElementBundle/Resources/scripts/accordions/CommentAccordion.js',
            '@PhlexibleElementBundle/Resources/scripts/accordions/ConfigurationAccordion.js',
            '@PhlexibleElementBundle/Resources/scripts/accordions/ContextAccordion.js',
            '@PhlexibleElementBundle/Resources/scripts/accordions/DataAccordion.js',
            '@PhlexibleElementBundle/Resources/scripts/accordions/DiffAccordion.js',
            '@PhlexibleElementBundle/Resources/scripts/accordions/InstancesAccordion.js',
            '@PhlexibleElementBundle/Resources/scripts/accordions/MetaAccordion.js',
            '@PhlexibleElementBundle/Resources/scripts/accordions/QuickInfo.js',
            '@PhlexibleElementBundle/Resources/scripts/accordions/VersionsAccordion.js',

            '@PhlexibleElementBundle/Resources/scripts/HistoryWindow.js',
            '@PhlexibleElementBundle/Resources/scripts/HistoryFilter.js',
            '@PhlexibleElementBundle/Resources/scripts/HistoryGrid.js',
            '@PhlexibleElementBundle/Resources/scripts/RightsGrid.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementAccordion.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementListGridFilter.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementListGrid.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementDataPanel.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementContentPanel.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementContentTabPanel.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementTabPanel.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementPanel.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementDataTabHelper.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementDataTab.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementLinksGrid.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementHistoryGrid.js',
            '@PhlexibleElementBundle/Resources/scripts/NewElementWindow.js',
            '@PhlexibleElementBundle/Resources/scripts/NewElementInstanceWindow.js',
            '@PhlexibleElementBundle/Resources/scripts/PublishTreeNodeWindow.js',
            '@PhlexibleElementBundle/Resources/scripts/SetTreeNodeOfflineWindow.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementDataWindow.js',
            '@PhlexibleElementBundle/Resources/scripts/EidSelector.js',
            '@PhlexibleElementBundle/Resources/scripts/LinkWindow.js',
            '@PhlexibleElementBundle/Resources/scripts/TaskBar.js',
            '@PhlexibleElementBundle/Resources/scripts/PublishSlaveWindow.js',
            '@PhlexibleElementBundle/Resources/scripts/DeleteInstancesWindow.js',
            '@PhlexibleElementBundle/Resources/scripts/MainPanel.js',
            '@PhlexibleElementBundle/Resources/scripts/TopToolbar.js',
            '@PhlexibleElementBundle/Resources/scripts/FileLinkWindow.js',
            '@PhlexibleElementBundle/Resources/scripts/UrlGrid.js',
            '@PhlexibleElementBundle/Resources/scripts/ElementPreviewPanel.js',
            '@PhlexibleElementBundle/Resources/scripts/LocksWindow.js',

            '@PhlexibleElementBundle/Resources/scripts/portlet/LatestElements.js',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return array(
            '@PhlexibleElementBundle/Resources/styles/elements.css',
            '@PhlexibleElementBundle/Resources/styles/portlet.css',
            '@PhlexibleElementBundle/Resources/styles/eidselector.css',
        );
    }
}
