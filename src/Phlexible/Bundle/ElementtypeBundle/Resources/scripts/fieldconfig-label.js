Ext.require('Phlexible.fields.Registry');
Ext.require('Phlexible.fields.FieldTypes');

Phlexible.fields.Registry.addFactory('label', function (parentConfig, item, valueStructure, element) {
    var contextHelp = item.labels.contextHelp || {},
        fieldLabel = item.labels.fieldLabel || {},
        config = {
            xtype: 'panel',
            html: item.content || contextHelp[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] || fieldLabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')],
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
            required: 0,
            field: 1,
            box: 0,
            prefix: 0,
            suffix: 0,
            help: 1
        },
        configuration: {

        }
    }
});
