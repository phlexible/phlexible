Ext.namespace('Phlexible.elementtypes.configuration');

Phlexible.elementtypes.configuration.FieldConfigurationDefaultValue = Ext.extend(Ext.form.FieldSet, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.values,
    iconCls: 'p-elementtype-tab_values-icon',
    autoHeight: true,
    defaultType: 'textfield',
    labelWidth: 139,

    initComponent: function () {
        this.items = [
            {
                fieldLabel: this.strings.default_value,
                name: 'default_value_textfield',
                hidden: true,
                disabled: true,
                width: 230
            },
            {
                xtype: 'numberfield',
                fieldLabel: this.strings.default_value,
                name: 'default_value_numberfield',
                hidden: true,
                disabled: true,
                width: 230
            },
            {
                xtype: 'textarea',
                fieldLabel: this.strings.default_value,
                name: 'default_value_textarea',
                hidden: true,
                disabled: true,
                width: 230,
                height: 100
            },
            {
                xtype: 'datefield',
                fieldLabel: this.strings.default_value,
                name: 'default_value_datefield',
                hidden: true,
                disabled: true,
                width: 183,
                format: 'Y-m-d'
            },
            {
                xtype: 'timefield',
                fieldLabel: this.strings.default_value,
                name: 'default_value_timefield',
                format: 'H:i:s',
                hidden: true,
                disabled: true,
                width: 183,
                listWidth: 200
            },
            {
                xtype: 'checkbox',
                fieldLabel: this.strings.default_value,
                boxLabel: 'checked',
                name: 'default_value_checkbox',
                hidden: true,
                disabled: true
            }
        ];

        Phlexible.elementtypes.configuration.FieldConfigurationDefaultValue.superclass.initComponent.call(this);
    },

    updateVisibility: function (type, fieldType) {
        if (!fieldType.config.values) {
            this.getComponent(0).disable();
            this.hide();
            return;
        }

        this.getComponent(0).enable();
        this.show();

        // default_text
        if (fieldType.config.values.default_text || fieldType.config.values.default_select || fieldType.config.values.default_link) {
            this.getComponent(0).enable();
            this.getComponent(0).show();
        }
        else {
            this.getComponent(0).disable();
            this.getComponent(0).hide();
        }

        // default_number
        if (fieldType.config.values.default_number) {
            this.getComponent(1).enable();
            this.getComponent(1).show();
        }
        else {
            this.getComponent(1).disable();
            this.getComponent(1).hide();
        }

        // default_textarea
        if (fieldType.config.values.default_textarea || fieldType.config.values.default_editor) {
            this.getComponent(2).enable();
            this.getComponent(2).show();
        }
        else {
            this.getComponent(2).disable();
            this.getComponent(2).hide();
        }

        // default_date
        if (fieldType.config.values.default_date) {
            this.getComponent(3).enable();
            this.getComponent(3).show();
        }
        else {
            this.getComponent(3).disable();
            this.getComponent(3).hide();
        }

        // default_time
        if (fieldType.config.values.default_time) {
            this.getComponent(4).enable();
            this.getComponent(4).show();
        }
        else {
            this.getComponent(4).disable();
            this.getComponent(4).hide();
        }

        // default_checkbox
        if (fieldType.config.values.default_checkbox) {
            this.getComponent(5).enable();
            this.getComponent(5).show();
        }
        else {
            this.getComponent(5).disable();
            this.getComponent(5).hide();
        }
    },

    loadData: function (fieldData, fieldType) {
        this.defaultValueField = null;

        this.items.each(function (item) {
            if (!item.isFormField) {
                return;
            }

            var name = item.getName();
            if (name == fieldType.defaultValueField) {
                item.setValue(fieldData.default_value);
                this.defaultValueField = fieldType.defaultValueField;
            }
            else {
                item.setValue('');
            }
        });

        this.isValid();
    },

    getSaveValues: function () {
        if (!this.defaultField) {
            return {};
        }

        var defaultValue = '';

        this.items.each(function(item) {
            if (item.name === this.defaultValueField) {
                defaultValue = item.getValue();
                return false;
            }
        }, this);

        return {
            default_value: defaultValue
        };
    },

    isValid: function () {
        return true;
    }
});
Ext.reg('elementtypes-configuration-field-configuration-default-value', Phlexible.elementtypes.configuration.FieldConfigurationDefaultValue);
