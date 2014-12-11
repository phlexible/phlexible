<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Elementtype asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeAssetProvider implements AssetProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getUxScriptsCollection()
    {
        $input = [
            '@PhlexibleElementtypeBundle/Resources/scripts-ux/Ext.ux.form.Accordion.js',
            '@PhlexibleElementtypeBundle/Resources/scripts-ux/Ext.ux.form.DisplayField.js',
            '@PhlexibleElementtypeBundle/Resources/scripts-ux/Ext.ux.form.Group.js',
            '@PhlexibleElementtypeBundle/Resources/scripts-ux/Ext.ux.form.LinkField.js',
            '@PhlexibleElementtypeBundle/Resources/scripts-ux/Ext.ux.form.TableField.js',
            '@PhlexibleElementtypeBundle/Resources/scripts-ux/Ext.ux.InputTextMask.js',
        ];

        return $input;
    }

    /**
     * {@inheritDoc}
     */
    public function getUxCssCollection()
    {
        $input = [
            '@PhlexibleElementtypeBundle/Resources/styles/ux/Ext.form.DisplayField.css',
        ];

        return $input;
    }

    /**
     * {@inheritDoc}
     */
    public function getScriptsCollection()
    {
        $input = [
            '@PhlexibleElementtypeBundle/Resources/scripts/Definitions.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/NewElementtypeWindow.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/FieldDragZone.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeStructureTreeRootNodeUI.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeStructureTreeNodeUI.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeStructureTreeLoader.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeStructureTreeDropZone.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeStructureTree.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeViability.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeUsage.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeRoot.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypeField.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/ElementtypesList.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/root/RootPropertyPanel.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/root/RootMappingsPanel.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/root/RootMappedTitleGrid.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/root/RootMappedDateGrid.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/root/RootMappedLinkGrid.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldConfiguration.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldLabel.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldProperty.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldValidation.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldContentchannel.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldConfigurationGroup.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldConfigurationAccordion.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldConfigurationDefaultValue.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldConfigurationLabel.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldConfigurationLink.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldConfigurationSelect.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldConfigurationSuggest.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/configuration/FieldConfigurationTable.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/configuration/SelectValueGrid.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/MainPanel.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/Definitions.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/FieldHelper.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/FieldTypes.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/Prototypes.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/Registry.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/container/Root.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/container/Tab.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/container/Accordion.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/container/Group.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/container/Reference.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/container/ReferenceRoot.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/field/Checkbox.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/field/DateField.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/field/DisplayField.js',
            //'@PhlexibleElementtypeBundle/Resources/scripts/field/HtmlEditor.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/field/Label.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/field/LinkField.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/field/MultiSelect.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/field/NumberField.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/field/Select.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/field/Suggest.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/field/Table.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/field/TextArea.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/field/TextField.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/field/TimecodeField.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/field/TimeField.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/model/Elementtype.js',
            '@PhlexibleElementtypeBundle/Resources/scripts/menuhandle/ElementtypesHandle.js',
        ];

        return $input;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $input = [
            '@PhlexibleElementtypeBundle/Resources/styles/elementtypes.css',
            '@PhlexibleElementtypeBundle/Resources/styles/portlet.css',
            '@PhlexibleElementtypeBundle/Resources/styles/fields.css',
        ];

        return $input;
    }
}
