Phlexible.fields.Registry.addFactory('editor', function (parentConfig, item, valueStructure, element, repeatableId) {
    element.prototypes.incCount(item.dsId);

    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, element, repeatableId);

    Ext.apply(config, {
        xtype: 'htmleditor'
    });

    Ext.each(valueStructure.values, function (value) {
        if (value.dsId === item.dsId) {
            config.value = value.content;
        }
    });

    if (item.configuration.readonly) {
        config.readOnly = true;
        config.ctCls = 'x-item-disabled';
    }

    return config;
});

Phlexible.fields.FieldTypes.addField('editor', {
    titles: {
        de: 'Editor',
        en: 'Editor'
    },
    iconCls: 'p-elementtype-field_editor-icon',
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
            height: 1,
            readonly: 1,
            hide_label: 1,
            sortable: 0
        },
        values: {
            default_text: 0,
            default_number: 0,
            default_textarea: 1,
            default_date: 0,
            default_time: 0,
            default_select: 0,
            default_link: 0,
            default_checkbox: 0,
            default_table: 0,
            source: 0,
            source_values: 0,
            source_function: 0,
            source_datasource: 0,
            text: 0
        },
        validation: {
            required: 1,
            text: 1,
            numeric: 0,
            content: 1
        }
    }
});
