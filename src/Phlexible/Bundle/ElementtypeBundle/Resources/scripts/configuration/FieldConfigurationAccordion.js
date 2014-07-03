Phlexible.elementtypes.configuration.FieldConfigurationAccordion = Ext.extend(Ext.form.FieldSet, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.accordion,
    iconCls: 'p-elementtype-container_accordion-icon',
    autoHeight: true,
    labelWidth: 139,

    initComponent: function () {
        this.items = [
            {
                xtype: 'checkbox',
                name: 'default_collapsed',
                fieldLabel: '',
                labelSeparator: '',
                boxLabel: this.strings.default_collapsed
            }
        ];

        Phlexible.elementtypes.configuration.FieldConfigurationAccordion.superclass.initComponent.call(this);
    },

    updateVisibility: function (type) {
        var isAccordion = type === 'accordion';
        this.getComponent(0).setDisabled(!isAccordion);
        this.setVisible(isAccordion);
    },

    loadData: function (fieldData, fieldType) {
        this.getComponent(0).setValue(fieldData.default_collapsed);

        this.isValid();
    },

    getSaveValues: function () {
        return {
            default_collapsed: this.getComponent(0).getValue()
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid();
    }
});

Ext.reg('elementtypes-configuration-field-configuration-accordion', Phlexible.elementtypes.configuration.FieldConfigurationAccordion);