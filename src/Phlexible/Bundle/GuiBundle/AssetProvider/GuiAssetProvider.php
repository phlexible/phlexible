<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Gui asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GuiAssetProvider implements AssetProviderInterface
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
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.ManagedIFrame.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.AutoGridPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.plugins.ToggleCollapsible.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.dd.GridReorderDropTarget.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.form.Spinner.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.form.Spinner.Strategy.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.SpinnerPlugin.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.form.XCheckbox.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.XmlTreeLoader.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.form.SuperBoxSelect.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.IconCombo.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.grid.CheckColumn.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.grid.CheckboxColumn.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.GUID.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.PasswordField.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.grid.RowExpander.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.GoogleChart.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.Notification.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.grid.RowActions.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.form.ColorField.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.TabPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.UploadDialog.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.Sortable.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.SliderTip.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.form.LovCombo.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.PanelBlind.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.tree.TreeFilterX.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.data.ObjectStore.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/ux/Ext.ux.WriteStore.js')),

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
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getUxCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.dd.GridReorderDropTarget.css')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.form.Spinner.css')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.IconCombo.css')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.PasswordField.css')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.Notification.css')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.grid.RowActions.css')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.form.ColorField.css')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.form.LovCombo.css')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.TabPanel.css')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.UploadDialog.css')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.PanelBlind.css')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.form.SuperBoxSelect.css')),
            // new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/ux/Ext.ux.GridRowDeleter.css')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getScriptsCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/dev.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/overrides.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/prototypes.js')),

            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/Phlexible.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/util/EntryManager.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/util/PluginRegistry.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/util/Router.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/Phlexible.functions.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/util/Console.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/Phlexible.cookie.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/Phlexible.Format.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/Definitions.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/util/Frame.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/util/SystemMessage.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/Phlexible.LoadHandler.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/PhpInfoWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/portlet/Load.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/util/Dialog.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/util/ImageSelectWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/util/Config.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/util/User.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/grid/TypeColumnModel.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/bundle/BundlesFilterPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/bundle/BundlesGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/bundle/BundlesMainPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/Menu.js')),

            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/Handle.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/Menu.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/BubbleMenu.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/Group.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/IframeHandle.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/FunctionHandle.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/HrefHandle.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/WindowHandle.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/handle/XtypeHandle.js')),

            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/Spacer.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/Separator.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/Fill.js')),

            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/MainGroup.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/MenusGroup.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/AccountGroup.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/TrayGroup.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/ConfigurationMenu.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/AdministrationMenu.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/DebugMenu.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/ToolsMenu.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/BundlesHandle.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/HelpHandle.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/PhpInfoHandle.js')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/scripts/menuhandle/PropertiesHandle.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/frame.css')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/extensions.css')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/util/Dialog.css')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/util/ImageSelectWindow.css')),
            new FileAsset($this->locator->locate('@PhlexibleGuiBundle/Resources/styles/overrides.css')),
        ));

        return $collection;
    }
}
