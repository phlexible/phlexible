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
                name: 'source',
                hiddenName: 'source',
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
                xtype: 'elementtypes-configuration-field-value-grid',
                hidden: true,
                listeners: {
                    defaultchange: function (key) {
                        this.getComponent(0).setValue(key);
                    },
                    scope: this
                }
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
                    url: Phlexible.Router.generate('elementtypes_data_select'),
                    root: 'functions',
                    fields: ['function', 'title']
                }),
                displayField: 'title',
                valueField: 'function',
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
        this.updateSelectSourceVisibility(fieldData.select_source);

        this.setVisible(type === 'select' || type === 'multiselect');
    },

    updateSelectSourceVisibility: function (source) {
        switch (source) {
            case 'list':
                this.getValueGrid().enable();
                this.getValueGrid().show();
                this.getComponent(2).disable();
                this.getComponent(2).hide();
                break;

            case 'function':
                this.getValueGrid().disable();
                this.getValueGrid().hide();
                this.getComponent(2).enable();
                this.getComponent(2).show();
                break;
        }
    },

    loadData: function (fieldData, fieldType) {
        this.getComponent(0).setValue(fieldData.select_source);
        this.getComponent(2).setValue(fieldData.select_function);

        this.getValueGrid().loadData(fieldData.select_list, fieldData.default_value);

        this.isValid();
    },

    getSaveValues: function () {
        var values = this.getForm().getValues();

        if (this.getValueGrid().isVisible()) {
            delete values.source_function;

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

            values.source_list = list;
        }

        return values;
    },

    isValid: function () {
        return this.getComponent(0).isValid() &&
            this.getComponent(1).isValid() &&
            this.getComponent(2).isValid();
    }
});

Ext.reg('elementtypes-configuration-field-configuration-select', Phlexible.elementtypes.configuration.FieldConfigurationSelect);