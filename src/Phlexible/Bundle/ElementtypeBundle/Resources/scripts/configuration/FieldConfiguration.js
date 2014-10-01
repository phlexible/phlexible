Phlexible.elementtypes.configuration.FieldConfiguration = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.configuration,
    iconCls: 'p-elementtype-tab_configuration-icon',
    border: false,
    autoScroll: true,
    bodyStyle: 'padding:3px',
    defaultType: 'textfield',
    labelWidth: 150,

    initComponent: function () {
        this.initMyItems();

        Phlexible.elementtypes.configuration.FieldConfiguration.superclass.initComponent.call(this);
    },

    initMyItems: function () {
        this.items = [
            {
                // 0
                xtype: 'combo',
                width: 183,
                listWidth: 200,
                fieldLabel: this.strings.required,
                hiddenName: 'required',
                store: new Ext.data.SimpleStore({
                    fields: ['key', 'value'],
                    data: [
                        ['no', this.strings.not_required],
                        ['on_publish', this.strings.on_publish],
                        ['always', this.strings.always]
                    ]
                }),
                displayField: 'value',
                valueField: 'key',
                editable: false,
                mode: 'local',
                triggerAction: 'all',
                selectOnFocus: true,
                typeAhead: false,
                value: 'no'
            },{
                // 1
                xtype: 'combo',
                fieldLabel: this.strings.language,
                hiddenName: 'synchronized',
                store: new Ext.data.SimpleStore({
                    fields: ['key', 'title'],
                    data: [
                        ['no', this.strings.not_synchronized],
                        ['synchronized', this.strings.synchronized],
                        ['synchronized_unlink', this.strings.synchronized_with_unlink]
                    ]
                }),
                width: 183,
                listWidth: 200,
                displayField: 'title',
                valueField: 'key',
                editable: false,
                mode: 'local',
                typeAhead: false,
                triggerAction: 'all'
            },
            {
                // 2
                xtype: 'numberfield',
                fieldLabel: this.strings.width,
                name: 'width',
                width: 200,
                allowDecimals: false,
                allowNegative: false,
                minValue: 20
            },
            {
                // 3
                xtype: 'numberfield',
                fieldLabel: this.strings.height,
                name: 'height',
                width: 200,
                allowDecimals: false,
                allowNegative: false,
                minValue: 20
            },
            {
                // 4
                xtype: 'checkbox',
                name: 'readonly',
                fieldLabel: '',
                labelSeparator: '',
                boxLabel: this.strings.readonly
            },
            {
                // 5
                xtype: 'checkbox',
                name: 'hide_label',
                fieldLabel: '',
                labelSeparator: '',
                boxLabel: this.strings.hide_label
            },
            {
                // 6
                xtype: 'checkbox',
                name: 'sortable',
                fieldLabel: this.strings.child_items,
                boxLabel: this.strings.can_be_sorted
            },
            {
                xtype: 'elementtypes-configuration-field-configuration-default-value',
                additional: true
            },
            {
                xtype: 'elementtypes-configuration-field-configuration-accordion',
                additional: true
            },
            {
                xtype: 'elementtypes-configuration-field-configuration-group',
                additional: true
            },
            {
                xtype: 'elementtypes-configuration-field-configuration-label',
                additional: true
            },
            {
                xtype: 'elementtypes-configuration-field-configuration-link',
                additional: true
            },
            {
                xtype: 'elementtypes-configuration-field-configuration-select',
                additional: true
            },
            {
                xtype: 'elementtypes-configuration-field-configuration-suggest',
                additional: true
            },
            {
                xtype: 'elementtypes-configuration-field-configuration-table',
                additional: true
            }
        ];
    },

    updateVisibility: function (fieldType, type, fieldData) {
        // language sync
        if (fieldType.config.configuration.required) {
            this.getComponent(0).show();
        }
        else {
            this.getComponent(0).hide();
        }

        // language sync
        if (fieldType.config.configuration.sync) {
            this.getComponent(1).show();
        }
        else {
            this.getComponent(1).hide();
        }

        // width
        if (fieldType.config.configuration.width) {
            this.getComponent(2).show();
        }
        else {
            this.getComponent(2).hide();
        }

        // height
        if (fieldType.config.configuration.height) {
            this.getComponent(3).show();
        }
        else {
            this.getComponent(3).hide();
        }

        // readonly
        if (fieldType.config.configuration.readonly) {
            this.getComponent(4).show();
        }
        else {
            this.getComponent(4).hide();
        }

        // hide label
        if (fieldType.config.configuration.hide_label) {
            this.getComponent(5).show();
        }
        else {
            this.getComponent(5).hide();
        }

        // children sortable
        if (fieldType.config.configuration.sortable) {
            this.getComponent(6).show();
        }
        else {
            this.getComponent(6).hide();
        }

        this.items.each(function (panel) {
            if (panel.additional) {
                panel.updateVisibility(type, fieldType, fieldData);
            }
        });
    },

    loadData: function (properties, type, fieldType) {
        var fieldData = properties.configuration;

        this.updateVisibility(fieldType, type, fieldData);

        this.getForm().setValues({
            required: fieldData.required || 'no',
            synchronized: fieldData.synchronized || 'no',
            width: fieldData.width,
            height: fieldData.height,
            sortable: fieldData.sortable,
            readonly: fieldData.readonly,
            hide_label: fieldData.hide_label
        });

        this.items.each(function (panel) {
            if (panel.additional) {
                panel.loadData(fieldData, fieldType);
            }
        });

        this.isValid();
    },

    getSaveValues: function () {
        var values = this.getForm().getValues(),
            data = {
                required: values.required,
                synchronized: values.synchronized,
                width: values.width,
                height: values.height,
                sortable: values.sortable,
                readonly: values.readonly,
                hide_label: values.hide_label
            };


        this.items.each(function (panel) {
            if (panel.additional && !panel.hidden) {
                Ext.apply(data, panel.getSaveValues());
            }
        });

        return data;
    },

    isValid: function () {
        var valid = this.getForm().isValid();

        if (valid) {
            this.items.each(function (panel) {
                if (panel.additional && panel.isVisible()) {
                    valid = valid && panel.isValid();
                    if (!valid) {
                        return false;
                    }
                }
            });
        }

        if (valid) {
            this.setIconClass('p-elementtype-tab_configuration-icon');

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
        if (fieldType.config.configuration) {
            this.active = true;
            this.ownerCt.getTabEl(this).hidden = false;
            this.loadData(properties, node.attributes.type, fieldType);
        }
        else {
            this.active = false;
            this.ownerCt.getTabEl(this).hidden = true;
        }
    }
});

Ext.reg('elementtypes-configuration-field-configuration', Phlexible.elementtypes.configuration.FieldConfiguration);