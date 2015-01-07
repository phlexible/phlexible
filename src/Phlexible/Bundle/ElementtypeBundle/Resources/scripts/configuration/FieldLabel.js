Ext.provide('Phlexible.elementtypes.configuration.FieldLabel');

Phlexible.elementtypes.configuration.FieldLabel = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.labels,
    iconCls: 'p-elementtype-tab_labels-icon',
    border: false,
    autoScroll: true,
    bodyStyle: 'padding:3px',
    defaultType: 'textfield',
    labelWidth: 120,

    initComponent: function () {
        this.items = [
            {
                xtype: 'fieldset',
                title: this.strings.fieldlabel,
                autoHeight: true,
                width: 350,
                items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: Phlexible.inlineIcon('p-flags-de-icon') + ' ' + this.strings.values_german,
                        name: 'fieldlabel_de',
                        allowBlank: false,
                        width: 200
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: Phlexible.inlineIcon('p-flags-en-icon') + ' ' + this.strings.values_english,
                        name: 'fieldlabel_en',
                        allowBlank: false,
                        width: 200
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: this.strings.boxlabel,
                autoHeight: true,
                width: 350,
                items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: Phlexible.inlineIcon('p-flags-de-icon') + ' ' + this.strings.values_german,
                        name: 'boxlabel_de',
                        width: 200
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: Phlexible.inlineIcon('p-flags-en-icon') + ' ' + this.strings.values_english,
                        name: 'boxlabel_en',
                        width: 200
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: this.strings.prefix,
                autoHeight: true,
                collapsible: true,
                collapsed: true,
                width: 350,
                items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: Phlexible.inlineIcon('p-flags-de-icon') + ' ' + this.strings.values_german,
                        name: 'prefix_de',
                        width: 200
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: Phlexible.inlineIcon('p-flags-en-icon') + ' ' + this.strings.values_english,
                        name: 'prefix_en',
                        width: 200
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: this.strings.suffix,
                autoHeight: true,
                collapsible: true,
                collapsed: true,
                width: 350,
                items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: Phlexible.inlineIcon('p-flags-de-icon') + ' ' + this.strings.values_german,
                        name: 'suffix_de',
                        width: 200
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: Phlexible.inlineIcon('p-flags-en-icon') + ' ' + this.strings.values_english,
                        name: 'suffix_en',
                        width: 200
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: this.strings.context_help,
                autoHeight: true,
                width: 350,
                items: [
                    {
                        xtype: 'textarea',
                        fieldLabel: Phlexible.inlineIcon('p-flags-de-icon') + ' ' + this.strings.values_german,
                        name: 'context_help_de',
                        width: 200
                    },
                    {
                        xtype: 'textarea',
                        fieldLabel: Phlexible.inlineIcon('p-flags-en-icon') + ' ' + this.strings.values_english,
                        name: 'context_help_en',
                        width: 200
                    }
                ]
            }
        ];

        Phlexible.elementtypes.configuration.FieldLabel.superclass.initComponent.call(this);
    },

    updateVisibility: function (fieldType) {
        // field
        if (fieldType.config.labels.field) {
            this.getComponent(0).show();
        }
        else {
            this.getComponent(0).hide();
        }

        // prefix
        if (fieldType.config.labels.box) {
            this.getComponent(1).show();
        }
        else {
            this.getComponent(1).hide();
        }
        // prefix
        if (fieldType.config.labels.prefix) {
            this.getComponent(2).show();
        }
        else {
            this.getComponent(2).hide();
        }

        // suffix
        if (fieldType.config.labels.suffix) {
            this.getComponent(3).show();
        }
        else {
            this.getComponent(3).hide();
        }

        // context
        if (fieldType.config.labels.help) {
            this.getComponent(4).show();
        }
        else {
            this.getComponent(4).hide();
        }
    },

    loadData: function (fieldData, fieldType) {
        this.updateVisibility(fieldType);

        fieldData.fieldLabel = fieldData.fieldLabel || {};
        fieldData.boxLabel = fieldData.boxLabel || {};
        fieldData.prefix = fieldData.prefix || {};
        fieldData.suffix = fieldData.suffix || {};
        fieldData.contextHelp = fieldData.contextHelp || {};

        this.getForm().setValues([
            {id: 'fieldlabel_de', value: fieldData.fieldLabel.de},
            {id: 'fieldlabel_en', value: fieldData.fieldLabel.en},
            {id: 'boxlabel_de', value: fieldData.boxLabel.de},
            {id: 'boxlabel_en', value: fieldData.boxLabel.en},
            {id: 'prefix_de', value: fieldData.prefix.de},
            {id: 'prefix_en', value: fieldData.prefix.en},
            {id: 'suffix_de', value: fieldData.suffix.de},
            {id: 'suffix_en', value: fieldData.suffix.en},
            {id: 'context_help_de', value: fieldData.contextHelp.de},
            {id: 'context_help_en', value: fieldData.contextHelp.en}
        ]);

        this.isValid();
    },

    getSaveValues: function () {
        var values = this.getForm().getValues();

        return {
            fieldLabel: {
                de: values.fieldlabel_de,
                en: values.fieldlabel_en
            },
            boxLabel: {
                de: values.boxlabel_de,
                en: values.boxlabel_en
            },
            prefix: {
                de: values.prefix_de,
                en: values.prefix_en
            },
            suffix: {
                de: values.suffix_de,
                en: values.suffix_en
            },
            contextHelp: {
                de: values.context_help_de,
                en: values.context_help_en
            }
        };
    },

    isValid: function () {
        if (this.getForm().isValid()) {
            this.setIconClass('p-elementtype-tab_labels-icon');

            return true;
        } else {
            this.setIconClass('p-elementtype-tab_error-icon');

            return false;
        }
    },

    isActive: function() {
        return !!this.active;
    },

    loadField: function (properties, node, fieldType) {
        if (fieldType.config.labels) {
            this.active = true;
            this.ownerCt.getTabEl(this).hidden = false;
            this.loadData(properties.labels, fieldType);
        }
        else {
            this.active = false;
            this.ownerCt.getTabEl(this).hidden = true;
        }
    }
});

Ext.reg('elementtypes-configuration-field-label', Phlexible.elementtypes.configuration.FieldLabel);