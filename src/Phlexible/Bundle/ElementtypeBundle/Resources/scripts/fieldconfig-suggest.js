Ext.require('Phlexible.fields.Registry');
Ext.require('Phlexible.fields.FieldTypes');

Phlexible.fields.Registry.addFactory('suggest', function (parentConfig, item, valueStructure, element, repeatableId) {
    var store, storeMode = 'local';

    if (item.configuration.suggest_source) {
        /*
         store = new Ext.data.SimpleStore({
         fields: ['key', 'value'],
         data: item.options
         });
         */
        storeMode = 'remote';
        store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('elementtypes_selectfield_suggest'),
            baseParams: {
                id: item.configuration.suggest_source,
                ds_id: item.dsId,
                language: element.language
            },
            fields: ['key', 'value'],
            root: 'data',
            autoLoad: false
        });
    } else {
        store = new Ext.data.SimpleStore({
            fields: ['key', 'value'],
            data: [
                ['no_valid_data', 'no_valid_data']
            ]
        });
    }

    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, valueStructure, element, repeatableId);

    Ext.apply(config, {
        xtype: 'elementtypes-field-suggest',
        name: config.name + '[]',
        width: (parseInt(item.configuration.width, 10) || 200),

        allowAddNewData: true,
        source_id: item.source_id,
        valueDelimiter: Phlexible.Config.get('suggest.seperator'),

        regex: /^[^,]+$/,
        hideMode: 'offsets',
        store: store,
        valueField: 'key',
        displayField: 'value',
        mode: storeMode,
        triggerAction: 'all',
        selectOnFocus: true,
        minChars: 2,
        supportsPrefix: true,
        supportsSuffix: true,
        supportsDiff: true,
        supportsUnlink: {styleEl: 'outerWrapEl', unlinkEl: 'wrap'},
        supportsRepeatable: true,
        listeners: {
            newitem: function (bs, v) {
                var newObj = {
                    key: v,
                    value: v
                };
                bs.addNewItem(newObj);
            },
            scope: this
        }
    });

    if (config.readOnly) {
        config.editable = false;
        config.hideTrigger = true;
        config.onTriggerClick = Ext.emptyFn;
    }

    return config;
});

Phlexible.fields.FieldTypes.addField('suggest', {
    titles: {
        de: 'Suggest',
        en: 'Suggest'
    },
    iconCls: 'p-elementtype-field_suggest-icon',
    allowedIn: [
        'tab',
        'accordion',
        'group',
        'referenceroot'
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
            required: 1,
            sync: 1,
            width: 1,
            height: 0,
            readonly: 1,
            hide_label: 1,
            sortable: 0
        }
    }
});
