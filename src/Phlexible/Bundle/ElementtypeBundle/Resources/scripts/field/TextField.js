Phlexible.fields.Registry.addFactory('textfield', function (parentConfig, item, valueStructure, element, repeatableId) {
    element.prototypes.incCount(item.dsId);

    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, element, repeatableId);

    Ext.apply(config, {
        xtype: 'textfield',
        minLength: (item.validation.min_length || 0),
        maxLength: (item.validation.max_length || Number.MAX_VALUE),
        vtype: (item.validation.validator || null),
        regex: (item.validation.regexp ? new RegExp(item.validation.regexp, (item.validation.ignore ? 'i' : '') + (item.validation.multiline ? 'm' : '')) : null),

        supportsPrefix: true,
        supportsSuffix: true,
        supportsDiff: true,
        supportsInlineDiff: true,
        supportsUnlink: true,
        supportsRepeatable: true
    });

    Ext.each(valueStructure.values, function (value) {
        if (value.dsId === item.dsId) {
            config.value = value.content;
        }
    });

    return config;
});

Phlexible.fields.FieldTypes.addField('textfield', {
    titles: {
        de: 'Textfeld',
        en: 'Textfield'
    },
    iconCls: 'p-elementtype-field_text-icon',
    allowedIn: [
        'tab',
        'accordion',
        'group',
        'referenceroot'
    ],
    defaultValueField: 'default_value_textfield',
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
            default_text: 1,
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
