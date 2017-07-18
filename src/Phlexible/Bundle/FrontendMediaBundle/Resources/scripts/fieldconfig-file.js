Ext.require('Phlexible.fields.Registry');
Ext.require('Phlexible.fields.FieldTypes');
Ext.require('Phlexible.frontendmedia.field.FileField');

Phlexible.fields.Registry.addFactory('file', function (parentConfig, item, valueStructure, element, repeatableId) {
    element.prototypes.incCount(item.dsId);

    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, valueStructure, element, repeatableId);

    var media = {};
    if (config.value && element.data.links) {
        var foundLink = null;
        Ext.each(element.data.links, function(link) {
            if (link.type === 'file' && link.raw === config.value) {
                foundLink = link;
                return false;
            }
        });

        if (foundLink) {
            media = {
                file_id: foundLink.payload.fileId,
                folder_id: foundLink.payload.folderId || '',
                folder_path: '/' + (foundLink.payload.folderPath ? foundLink.payload.folderPath.join('/') : ''),
                name: foundLink.payload.name || ''
            };
        }
    }

    Ext.apply(config, {
        xtype: 'filefield',
        data_id: item.data_id,

        file_id: media.file_id || false,
        folder_id: media.folder_id || false,
        folder_path: media.folder_path || false,
        fileTitle: media.name,

        mediaCategory: item.configuration.mediaCategory || '',
        mediaTypes: item.configuration.mediaTypes || '',
        viewMode: item.configuration.viewMode || 'tile',

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
    allowMap: true,
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
            width: 0,
            height: 0,
            readonly: 1,
            hide_label: 1,
            sortable: 0
        }
    }
});
