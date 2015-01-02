Ext.namespace('Phlexible.elementtypes.configuration');

Phlexible.elementtypes.configuration.FieldConfigurationLabel = Ext.extend(Ext.form.FieldSet, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.label,
    iconCls: 'p-elementtype-field_label-icon',
    autoHeight: true,
    labelWidth: 139,

    initComponent: function () {
        this.items = [
            {
                xtype: 'textarea',
                fieldLabel: Phlexible.inlineIcon('p-flags-de-icon') + ' ' + this.strings.values_german,
                name: 'text_de',
                width: 200
            },
            {
                xtype: 'textarea',
                fieldLabel: Phlexible.inlineIcon('p-flags-en-icon') + ' ' + this.strings.values_english,
                name: 'text_en',
                width: 200
            }
        ];

        Phlexible.elementtypes.configuration.FieldConfigurationLabel.superclass.initComponent.call(this);
    },

    updateVisibility: function (type) {
        var isLabel = (type === 'label' || type === 'displayfield');
        this.getComponent(0).setDisabled(!isLabel);
        this.getComponent(1).setDisabled(!isLabel);
        this.setVisible(isLabel);
    },

    loadData: function (fieldData, fieldType) {
        this.getComponent(0).setValue(fieldData.text_de);
        this.getComponent(1).setValue(fieldData.text_en);

        this.isValid();
    },

    getSaveValues: function () {
        return {
            text_de: this.getComponent(0).getValue() || '',
            text_en: this.getComponent(1).getValue() || ''
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid()
            && this.getComponent(1).isValid();
    }
});

Ext.reg('elementtypes-configuration-field-configuration-label', Phlexible.elementtypes.configuration.FieldConfigurationLabel);