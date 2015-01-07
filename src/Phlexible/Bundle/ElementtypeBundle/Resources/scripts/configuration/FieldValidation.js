Ext.provide('Phlexible.elementtypes.configuration.FieldValidation');

Phlexible.elementtypes.configuration.FieldValidation = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.validation,
    iconCls: 'p-elementtype-tab_validation-icon',
    border: false,
    autoScroll: true,
    bodyStyle: 'padding:3px',
    defaultType: 'textfield',
    labelWidth: 120,

    initComponent: function () {
        this.items = [
            {
                xtype: 'fieldset',
                title: this.strings.text_validation,
//            disabled: true,
                autoHeight: true,
                maskDisabled: false,
                width: 350,
                items: [
                    {
                        xtype: 'uxspinner',
//                xtype: 'numberfield',
                        fieldLabel: this.strings.text_min_length,
                        name: 'min_length',
                        width: 183,
                        maskRe: /[0-9]/,
                        strategy: new Ext.ux.form.Spinner.NumberStrategy({minValue: 0})
                    },
                    {
                        xtype: 'uxspinner',
//                xtype: 'numberfield',
                        name: 'max_length',
                        fieldLabel: this.strings.text_max_length,
                        width: 183,
                        maskRe: /[0-9]/,
                        strategy: new Ext.ux.form.Spinner.NumberStrategy({minValue: 0})
                    },
                    {
                        xtype: 'textfield',
                        name: 'regexp',
                        fieldLabel: this.strings.text_regular_expression,
                        width: 200
                    },
                    {
                        xtype: 'checkboxgroup',
                        name: 'modifiers',
                        fieldLabel: this.strings.text_modifiers,
                        items: [
                            {
                                boxLabel: this.strings.text_global,
                                name: 'global',
                                hidden: true
                            },
                            {
                                boxLabel: this.strings.text_ignore_case,
                                name: 'ignore'
                            },
                            {
                                boxLabel: this.strings.text_multiline,
                                name: 'multiline'
                            }
                        ]
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: this.strings.content_validation,
//            disabled: true,
                autoHeight: true,
                maskDisabled: false,
                width: 350,
                items: [
                    {
                        xtype: 'combo',
                        fieldLabel: this.strings.text_validator,
                        width: 183,
                        listWidth: 200,
                        hiddenName: 'validator',
                        store: new Ext.data.SimpleStore({
                            fields: ['key', 'value'],
                            data: [
                                ['', 'No validator'],
                                ['alpha', 'Alpha'],
                                ['alphanum', 'Alphanumeric'],
                                ['email', 'Email'],
                                ['url', 'Url']
                            ]
                        }),
                        editable: false,
                        mode: 'local',
                        displayField: 'value',
                        valueField: 'key',
                        triggerAction: 'all',
                        selectOnFocus: true,
                        typeAhead: false,
                        value: ''
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: this.strings.number_validation,
                autoHeight: true,
//            disabled: true,
                maskDisabled: false,
                width: 350,
                items: [
                    {
                        xtype: 'checkbox',
                        fieldLabel: this.strings.value,
                        boxLabel: this.strings.number_allow_negative,
                        name: 'allow_negative'
                    },
                    {
                        xtype: 'checkbox',
                        fieldLabel: '',
                        labelSeparator: '',
                        boxLabel: this.strings.number_allow_decimals,
                        name: 'allow_decimals'
                    },
                    {
                        xtype: 'uxspinner',
//                xtype: 'numberfield',
                        fieldLabel: this.strings.number_min_value,
                        name: 'min_value',
                        width: 183,
                        maskRe: /[0-9]/,
                        strategy: new Ext.ux.form.Spinner.NumberStrategy({minValue: 0})
                    },
                    {
                        xtype: 'uxspinner',
//                xtype: 'numberfield',
                        fieldLabel: this.strings.number_max_value,
                        name: 'max_value',
                        width: 183,
                        maskRe: /[0-9]/,
                        strategy: new Ext.ux.form.Spinner.NumberStrategy({minValue: 0})
                    }
                ]
            }
        ];

        Phlexible.elementtypes.configuration.FieldValidation.superclass.initComponent.call(this);
    },

    updateVisibility: function (fieldType) {
        // text
        if (fieldType.config.validation.text) {
            this.getComponent(0).show();
        }
        else {
            this.getComponent(0).hide();
        }

        // content
        if (fieldType.config.validation.content) {
            this.getComponent(1).show();
        }
        else {
            this.getComponent(1).hide();
        }

        // numeric
        if (fieldType.config.validation.numeric) {
            this.getComponent(2).show();
        }
        else {
            this.getComponent(2).hide();
        }
    },

    loadData: function (fieldData, fieldType) {
        this.updateVisibility(fieldType);

        var text = this.getComponent(0);
        var content = this.getComponent(1);
        var number = this.getComponent(2);

        text.getComponent(0).setValue(fieldData.min_length);
        text.getComponent(1).setValue(fieldData.max_length);
        text.getComponent(2).setValue(fieldData.regexp);
        text.getComponent(3).items.items[0].setValue(fieldData.global);
        text.getComponent(3).items.items[1].setValue(fieldData.ignore);
        text.getComponent(3).items.items[2].setValue(fieldData.multiline);
        content.getComponent(0).setValue(fieldData.validator || '');
        number.getComponent(0).setValue(fieldData.allow_negative);
        number.getComponent(1).setValue(fieldData.allow_decimals);
        number.getComponent(2).setValue(fieldData.min_value);
        number.getComponent(3).setValue(fieldData.max_value);
    },

    getSaveValues: function () {
        var values = this.getForm().getValues();

        return {
            min_length: values.min_length,
            max_length: values.max_length,
            regexp: values.regexp,
            global: values.global,
            ignore: values.ignore,
            multiline: values.multiline,
            validator: values.validator,
            allow_negative: values.allow_negative,
            allow_decimals: values.allow_decimals,
            min_value: values.min_value,
            max_value: values.max_value
        };
    },

    isValid: function () {
        if (this.getForm().isValid()) {
            this.setIconClass('p-elementtype-tab_validation-icon');

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
        if (fieldType.config.validation) {
            this.active = true;
            this.ownerCt.getTabEl(this).hidden = false;
            this.loadData(properties.validation, fieldType);
        }
        else {
            this.active = false;
            this.ownerCt.getTabEl(this).hidden = true;
        }
    }
});

Ext.reg('elementtypes-configuration-field-validation', Phlexible.elementtypes.configuration.FieldValidation);