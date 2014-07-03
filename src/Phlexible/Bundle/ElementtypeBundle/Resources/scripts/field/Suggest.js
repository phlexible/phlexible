Phlexible.fields.Suggest = Ext.extend(Ext.ux.form.SuperBoxSelect, {
    onResize: function (w, h, rw, rh) {
        Phlexible.fields.Suggest.superclass.onResize.call(this, w, h, rw, rh);

        this.wrap.setWidth(this.width + 20);
    }
});
Ext.reg('elementtypes-field-suggest', Phlexible.fields.Suggest);

Phlexible.fields.Registry.addFactory('suggest', function (parentConfig, item, valueStructure, pos, element, repeatablePostfix, forceAdd) {
    if (element.master) {
        element.prototypes.addFieldPrototype(item);
    }

    var store;
    var storeMode = 'local';
    if (item.options) {
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

    element.prototypes.incCount(item.dsId);

    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, element, repeatablePostfix, forceAdd);

    Ext.apply(config, {
        xtype: 'elementtypes-field-suggest',
        name: config.name + '[]',
        value: item.rawContent || '',
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
            default_select: 0,
            default_link: 0,
            default_checkbox: 0,
            default_table: 0,
            source: 0,
            source_values: 0,
            source_function: 0,
            source_datasource: 1,
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
