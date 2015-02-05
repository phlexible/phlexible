Ext.provide('Phlexible.elementtypes.configuration.FieldConfigurationSelect');

Ext.require('Phlexible.elementtypes.configuration.SelectValueGrid');

Phlexible.elementtypes.configuration.FieldConfigurationSelect = Ext.extend(Ext.form.FieldSet, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.select,
    iconCls: 'p-elementtype-field_select-icon',
    autoHeight: true,
    labelWidth: 139,

    initComponent: function () {
        this.items = [
            {
                xtype: 'combo',
                fieldLabel: this.strings.source,
                name: 'select_source',
                hiddenName: 'select_source',
                hideMode: 'display',
                allowBlank: false,
                store: new Ext.data.SimpleStore({
                    fields: ['key', 'title'],
                    data: [
                        ['list', this.strings.editable_list],
                        ['function', this.strings.component_function]
                    ]
                }),
                displayField: 'title',
                valueField: 'key',
                editable: false,
                mode: 'local',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                width: 183,
                listWidth: 200,
                listeners: {
                    select: function (combo, record, index) {
                        var source = record.get('key');
                        this.updateSelectSourceVisibility(source);
                    },
                    scope: this
                }
            },
            {
                xtype: 'elementtypes-configuration-select-value-grid',
                hidden: true
            },
            {
                xtype: 'combo',
                hidden: true,
                editable: false,
                hiddenName: 'source_function',
                name: 'source_function',
                fieldLabel: 'Function',
                hideMode: 'display',
                allowBlank: false,
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('elementtypes_selectfield_providers'),
                    root: 'functions',
                    fields: ['name', 'title'],
                    autoLoad: true,
                    listeners: {
                        load: function() {
                            this.getComponent(2).setValue(this.getComponent(2).getValue());
                        },
                        scope: this
                    }
                }),
                displayField: 'title',
                valueField: 'name',
                mode: 'remote',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                listWidth: 200,
                width: 182
            }
        ];

        Phlexible.elementtypes.configuration.FieldConfigurationSelect.superclass.initComponent.call(this);
    },

    getValueGrid: function () {
        return this.getComponent(1);
    },

    updateVisibility: function (type, fieldType, fieldData) {
        var isSelect = (type === 'select' || type === 'multiselect');
        this.getComponent(0).setDisabled(!isSelect);
        this.getComponent(2).setDisabled(!isSelect);
        this.setVisible(isSelect);

        this.updateSelectSourceVisibility(fieldData.select_source);
    },

    updateSelectSourceVisibility: function (source) {
        switch (source) {
            case 'list':
                this.getComponent(1).enable();
                this.getComponent(1).show();
                this.getComponent(2).disable();
                this.getComponent(2).hide();
                break;

            case 'function':
                this.getComponent(1).disable();
                this.getComponent(1).hide();
                this.getComponent(2).enable();
                this.getComponent(2).show();
                break;

            default:
                this.getComponent(1).disable();
                this.getComponent(1).hide();
                this.getComponent(2).disable();
                this.getComponent(2).hide();
        }
    },

    loadData: function (fieldData, fieldType) {
        this.getComponent(0).setValue(fieldData.select_source);
        this.getComponent(2).setValue(fieldData.select_function);

        this.getValueGrid().loadData(fieldData.select_list, fieldData.default_value);

        this.isValid();
    },

    getSaveValues: function () {
        var data = {
            select_source: this.getComponent(0).getValue(),
            select_function: this.getComponent(2).getValue(),
            select_list: null,
            default_value: null
        };

        if (this.getValueGrid().isVisible()) {
            data.source_function = null;;

            var list = [];

            for (var i = 0; i < this.getValueGrid().store.getCount(); i++) {
                var r = this.getValueGrid().store.getAt(i);
                list.push({
                    key: r.get('key'),
                    de: r.get('value_de'),
                    en: r.get('value_en')
                });
            }

            this.getValueGrid().store.commitChanges();

            data.select_list = list;
            data.default_value = this.getValueGrid().getDefaultValue();
        }

        return data;
    },

    isValid: function () {
        return this.getComponent(0).isValid() &&
            this.getComponent(1).isValid() &&
            this.getComponent(2).isValid();
    }
});

Ext.reg('elementtypes-configuration-field-configuration-select', Phlexible.elementtypes.configuration.FieldConfigurationSelect);