Phlexible.fields.Registry.addFactory('select', function (parentConfig, item, valueStructure, element, repeatableId) {
    var store, storeMode = 'remote';
    if (item.options.source === 'component_function') {
        store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('elementtypes_selectfield_list'),
            baseParams: {
                provider: item.options.component_function
            },
            fields: ['key', 'value'],
            sortInfo: {
                field: 'value', direction: 'ASC'
            },
            root: 'data',
            autoLoad: true,
            listeners: {
                load: function () {
                    newItem.setValue(item.options.default_value);
                },
                scope: this
            }
        });
    } else if (item.options.source === 'list') {
        var storeData = [];
        if (item.options.source_list.length) {
            for (var i = 0; i < item.options.source_list.length; i++) {
                storeData.push([item.options.source_list[i].key, item.options.source_list[i][Phlexible.Config.get('user.property.interfaceLanguage', 'en')]]);
            }
        } else {
            storeData.push(['no_valid_data', 'no_valid_data']);
        }
        store = new Ext.data.SimpleStore({
            fields: ['key', 'value'],
            data: storeData
        });
        storeMode = 'local';
    }

    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, valueStructure, element, repeatableId);

    Ext.apply(config, {
        xtype: 'twincombobox',
        hiddenName: config.name,
        width: (parseInt(item.configuration.width, 10) || 200),
        listWidth: (parseInt(item.configuration.width, 10) || 200) - 17,

        store: store,
        valueField: 'key',
        displayField: 'value',
        mode: storeMode,
        typeAhead: false,
        editable: false,
        triggerAction: 'all',
        selectOnFocus: true,
        hideMode: 'offsets',

        supportsPrefix: true,
        supportsSuffix: true,
        supportsDiff: true,
        supportsInlineDiff: true,
        supportsUnlink: true,
        supportsRepeatable: true
    });

    delete config.name;

    if (config.readOnly) {
        config.hideTrigger1 = true;
        config.hideTrigger2 = true;
        config.onTrigger1Click = Ext.emptyFn;
        config.onTrigger2Click = Ext.emptyFn;
    }

    return config;
});

Phlexible.fields.FieldTypes.addField('select', {
    titles: {
        de: 'Select',
        en: 'Select'
    },
    iconCls: 'p-elementtype-field_select-icon',
    allowedIn: [
        'tab',
        'accordion',
        'group',
        'referenceroot'
    ],
    defaultValueField: 'default_value_textfield',
    copyFields: [
        'list',
        'component_function'
    ],
    config: {
        labels: {
            field: 1,
            box: 0,
            prefix: 1,
            suffix: 1,
            help: 1
        },
        configuration: {
            sync: 1,
            width: 1,
            height: 0,
            readonly: 1,
            hide_label: 1,
            sortable: 0
        },
        values: {
            default_text: 0,
            default_number: 0,
            default_textarea: 0,
            default_date: 0,
            default_time: 0,
            default_select: 1,
            default_link: 0,
            default_checkbox: 0,
            default_table: 0,
            source: 1,
            source_multi: 1,
            source_values: 1,
            source_function: 1,
            source_datasource: 0,
            text: 0
        },
        validation: {
            required: 1,
            text: 0,
            numeric: 0,
            content: 0
        }
    }
});
