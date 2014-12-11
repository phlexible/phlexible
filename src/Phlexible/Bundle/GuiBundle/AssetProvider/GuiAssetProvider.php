<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\AssetProvider;

/**
 * Gui asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GuiAssetProvider implements AssetProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getUxScriptsCollection()
    {
        return array(
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.ManagedIFrame.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.AutoGridPanel.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.plugins.ToggleCollapsible.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.dd.GridReorderDropTarget.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.form.Spinner.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.form.Spinner.Strategy.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.SpinnerPlugin.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.form.XCheckbox.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.XmlTreeLoader.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.form.SuperBoxSelect.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.TwinComboBox.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.IconCombo.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.TwinIconCombo.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.grid.CheckColumn.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.grid.CheckboxColumn.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.GUID.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.PasswordField.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.grid.RowExpander.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.GoogleChart.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.Notification.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.grid.RowActions.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.form.ColorField.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.TabPanel.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.UploadDialog.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.Sortable.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.SliderTip.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.form.LovCombo.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.PanelBlind.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.tree.TreeFilterX.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.data.ObjectStore.js',
            '@PhlexibleGuiBundle/Resources/scripts-ux/Ext.ux.WriteStore.js',

            // $scriptDir.'Ext.ux.MultiSelectTextField.js',
            // $scriptDir.'Ext.ux.layout.RowFitLayout.js',
            // $scriptDir.'Ext.ux.GridRowDeleter.js',
            // $scriptDir.'Ext.ux.InlineToolbarTabPanel.js',
            // $scriptDir.'Ext.tree.ColumnTree.js',
            // $scriptDir.'Ext.util.MD5.js',
            // $scriptDir.'Ext.util.Utf8.js',

            // $scriptDir.'filter2/menu/EditableItem.js',
            // $scriptDir.'filter2/menu/RangeMenu.js',

            // $scriptDir.'filter2/GridFilters.js',
            // $scriptDir.'filter2/filter/Filter.js',
            // $scriptDir.'filter2/filter/BooleanFilter.js',
            // $scriptDir.'filter2/filter/DateFilter.js',
            // $scriptDir.'filter2/filter/ListFilter.js',
            // $scriptDir.'filter2/filter/NumericFilter.js',
            // $scriptDir.'filter2/filter/StringFilter.js',

            // $scriptDir.'editor/htmleditor.js',
            // $scriptDir.'editor/htmleditorimage.js',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getUxCssCollection()
    {
        return array(
            '@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.dd.GridReorderDropTarget.css',
            '@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.form.Spinner.css',
            '@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.IconCombo.css',
            '@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.PasswordField.css',
            '@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.Notification.css',
            '@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.grid.RowActions.css',
            '@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.form.ColorField.css',
            '@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.form.LovCombo.css',
            '@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.TabPanel.css',
            '@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.UploadDialog.css',
            '@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.PanelBlind.css',
            '@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.form.SuperBoxSelect.css',
            //'@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.GridRowDeleter.css',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getScriptsCollection()
    {
        return array(
            '@PhlexibleGuiBundle/Resources/scripts/dev.js',
            '@PhlexibleGuiBundle/Resources/scripts/overrides.js',
            '@PhlexibleGuiBundle/Resources/scripts/prototypes.js',

            '@PhlexibleGuiBundle/Resources/scripts/Phlexible.js',
            '@PhlexibleGuiBundle/Resources/scripts/util/EntryManager.js',
            '@PhlexibleGuiBundle/Resources/scripts/util/PluginRegistry.js',
            '@PhlexibleGuiBundle/Resources/scripts/util/Router.js',
            '@PhlexibleGuiBundle/Resources/scripts/Phlexible.functions.js',
            '@PhlexibleGuiBundle/Resources/scripts/util/Console.js',
            '@PhlexibleGuiBundle/Resources/scripts/Phlexible.cookie.js',
            '@PhlexibleGuiBundle/Resources/scripts/Phlexible.Format.js',
            '@PhlexibleGuiBundle/Resources/scripts/Definitions.js',
            '@PhlexibleGuiBundle/Resources/scripts/util/Frame.js',
            '@PhlexibleGuiBundle/Resources/scripts/util/SystemMessage.js',
            '@PhlexibleGuiBundle/Resources/scripts/Phlexible.LoadHandler.js',
            '@PhlexibleGuiBundle/Resources/scripts/PhpInfoWindow.js',
            '@PhlexibleGuiBundle/Resources/scripts/portlet/Load.js',
            '@PhlexibleGuiBundle/Resources/scripts/util/Dialog.js',
            '@PhlexibleGuiBundle/Resources/scripts/util/ImageSelectWindow.js',
            '@PhlexibleGuiBundle/Resources/scripts/util/Config.js',
            '@PhlexibleGuiBundle/Resources/scripts/util/User.js',
            '@PhlexibleGuiBundle/Resources/scripts/grid/TypeColumnModel.js',
            '@PhlexibleGuiBundle/Resources/scripts/bundle/BundlesFilterPanel.js',
            '@PhlexibleGuiBundle/Resources/scripts/bundle/BundlesGrid.js',
            '@PhlexibleGuiBundle/Resources/scripts/bundle/BundlesMainPanel.js',
            '@PhlexibleGuiBundle/Resources/scripts/Menu.js',
            '@PhlexibleGuiBundle/Resources/scripts/Help.js',

            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/Handle.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/Menu.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/BubbleMenu.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/Group.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/IframeHandle.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/FunctionHandle.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/HrefHandle.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/WindowHandle.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/XtypeHandle.js',

            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/Spacer.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/Separator.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/Fill.js',

            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/MainGroup.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/MenusGroup.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/AccountGroup.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/TrayGroup.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/ConfigurationMenu.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/AdministrationMenu.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/DebugMenu.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/ToolsMenu.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/BundlesHandle.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/HelpHandle.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/PhpInfoHandle.js',
            '@PhlexibleGuiBundle/Resources/scripts/menuhandle/PropertiesHandle.js',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return array(
            '@PhlexibleGuiBundle/Resources/styles/frame.css',
            '@PhlexibleGuiBundle/Resources/styles/extensions.css',
            '@PhlexibleGuiBundle/Resources/styles/util/Dialog.css',
            '@PhlexibleGuiBundle/Resources/styles/util/ImageSelectWindow.css',
            '@PhlexibleGuiBundle/Resources/styles/overrides.css',
        );
    }
}
