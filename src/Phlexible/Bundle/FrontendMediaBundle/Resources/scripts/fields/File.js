Phlexible.fields.Registry.addFactory('file', function (parentConfig, item, valueStructure, element, repeatableId) {
    element.prototypes.incCount(item.dsId);

    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, valueStructure, element, repeatableId);

    // TODO: wie?
    item.media = item.media || {};

    Ext.apply(config, {
        xtype: 'filefield',
        data_id: item.data_id,

        file_id: item.media.file_id || false,
        folder_id: item.media.folder_id || false,
        folder_path: item.media.folder_path || false,
        fileTitle: item.media.name,

        supportsPrefix: true,
        supportsSuffix: true,
        supportsDiff: true,
        supportsRepeatable: true
    });

    delete config.width;
    delete config.height;

    return config;
});

Phlexible.fields.FieldTypes.addField('file', {
    titles: {
        de: 'Datei',
        en: 'File'
    },
    iconCls: 'p-frontendmedia-field_file-icon',
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
            width: 0,
            height: 0,
            readonly: 1,
            hide_label: 1,
            sortable: 0
        },
        validation: {
            required: 1,
            text: 0,
            numeric: 0,
            content: 0
        }
    }
});