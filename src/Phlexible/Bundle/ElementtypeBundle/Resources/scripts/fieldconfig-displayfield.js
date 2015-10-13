Ext.require('Phlexible.fields.Registry');
Ext.require('Phlexible.fields.FieldTypes');
Ext.require('Phlexible.elementtypes.field.DisplayField');

Phlexible.fields.Registry.addFactory('displayfield', function (parentConfig, item, valueStructure, element, repeatableId) {
    // labels
    var hideLabel,
        label,
        labelSeparator = ':',
        language = Phlexible.Config.get('user.property.interfaceLanguage', 'en'),
        configuration = item.configuration,
        contextHelp = item.labels.contextHelp || {},
        prefix = item.labels.prefix || {},
        suffix = item.labels.suffix || {};

    if (parentConfig.singleLineLabel) {
        label = parentConfig.singleLineLabel;
        parentConfig.singleLineLabel = '';
        hideLabel = false;
    } else if (parentConfig.singleLine) {
        hideLabel = true;
        label = item.labels.fieldLabel[language];
    } else if (item.configuration.hide_label) {
        hideLabel = false;
        label = '';
        labelSeparator = '';
    } else {
        hideLabel = false;
        label = item.labels.fieldLabel[language];
    }

    var field_prefix = 'field_' + item.dsId + '_';
    if (item.data_id) {
        field_prefix += 'id-' + item.data_id;
    } else {
        field_prefix += Ext.id(null, 'new');
    }

    var config = {
        xtype: 'displayfield',
        name: field_prefix + (repeatableId ? '#' + repeatableId : ''),
        dsId: item.dsId,

        fieldLabel: label,
        helpText: contextHelp[language] || '',
        prefix: prefix[language] || '',
        suffix: suffix[language] || '',
        labelSeparator: labelSeparator,
        hideLabel: hideLabel,
        value: item.content || configuration['text_' + language],
        width: item.configuration.width || 100,
        element: element,

        supportsPrefix: true,
        supportsSuffix: true
    };

    Ext.each(valueStructure.values, function (value) {
        if (value.dsId === item.dsId) {
            config.value = value.content;
        }
    });

    return config;
});

Phlexible.fields.FieldTypes.addField('displayfield', {
    titles: {
        de: 'Anzeigefeld',
        en: 'Displayfield'
    },
    iconCls: 'p-elementtype-field_display-icon',
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
        configuration: {
            required: 0,
            sync: 0,
            width: 1,
            height: 0,
            readonly: 0,
            hide_label: 1,
            sortable: 0
        }
    }
});
