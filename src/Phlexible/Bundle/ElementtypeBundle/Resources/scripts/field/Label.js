Phlexible.fields.Registry.addFactory('label', function (parentConfig, item, valueStructure, element) {
    element.prototypes.incCount(item.dsId);

    var config = {
        xtype: 'panel',
        html: item.content || item.labels.context_help[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] || item.labels.fieldlabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')],
        plain: true,
        border: false,
        cls: 'p-fields-label'
    };

    Ext.each(valueStructure.values, function (value) {
        if (value.dsId === item.dsId) {
            config.value = value.content;
        }
    });

    return config;
});

Phlexible.fields.FieldTypes.addField('label', {
    titles: {
        de: 'Label',
        en: 'Label'
    },
    iconCls: 'p-elementtype-field_label-icon',
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
            prefix: 0,
            suffix: 0,
            help: 1
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
            source_datasource: 0,
            text: 1
        }
    }
});
