<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Elementtype asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeAssetProvider implements AssetProviderInterface
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
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/ux/Ext.ux.form.Accordion.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/ux/Ext.ux.form.DisplayField.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/ux/Ext.ux.form.Group.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/ux/Ext.ux.form.LinkField.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/ux/Ext.ux.form.TableField.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/ux/Ext.ux.InputTextMask.js'
            )),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getUxCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/styles/ux/Ext.form.DisplayField.css'
            )),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getScriptsCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/Definitions.js')),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/NewElementtypeWindow.js'
            )),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/FieldDragZone.js')),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeStructureTreeRootNodeUI.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeStructureTreeNodeUI.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeStructureTreeLoader.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeStructureTreeDropZone.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeStructureTree.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeVersions.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeViability.js'
            )),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeUsage.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/ElementtypesList.js')),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/root/RootPropertyPanel.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/root/RootMappingsPanel.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/root/RootMappedTitleGrid.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/root/RootMappedDateGrid.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/root/RootMappedLinkGrid.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldConfiguration.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldLabel.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldProperty.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldValidation.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldValue.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldContentchannel.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldConfigurationGroup.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldConfigurationAccordion.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldConfigurationLink.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldConfigurationTable.js'
            )),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeRoot.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeField.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/MainPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/Definitions.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/FieldHelper.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/FieldTypes.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/Prototypes.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/Registry.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/container/Root.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/container/Tab.js')),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/container/Accordion.js'
            )),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/container/Group.js')),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/container/Reference.js'
            )),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/container/ReferenceRoot.js'
            )),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/field/Checkbox.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/field/DateField.js')),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/field/DisplayField.js'
            )),
            //new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/field/HtmlEditor.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/field/Label.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/field/LinkField.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/field/MultiSelect.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/field/NumberField.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/field/Select.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/field/Suggest.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/field/Table.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/field/TextArea.js')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/field/TextField.js')),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/field/TimecodeField.js'
            )),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/scripts/field/TimeField.js')),
            new FileAsset($this->locator->locate(
                '@PhlexibleElementtypeBundle/Resources/scripts/menuhandle/ElementtypesHandle.js'
            )),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/styles/elementtypes.css')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/styles/portlet.css')),
            new FileAsset($this->locator->locate('@PhlexibleElementtypeBundle/Resources/styles/fields.css')),
        ));

        return $collection;
    }
}
