Phlexible.fields.Registry.addFactory('folder', function(parentConfig, item, valueStructure, pos, element, repeatablePostfix, forceAdd) {
	if (element.master) {
		element.prototypes.addFieldPrototype(item);
	}

	element.prototypes.incCount(item.ds_id);

	var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, element, repeatablePostfix, forceAdd);

	// TODO: wie?
	item.media = item.media || {};

	Ext.apply(config, {
		xtype: 'folderfield',
		hiddenName: config.name,
		data_id: item.data_id,

		width: (parseInt(item.configuration.width, 10) || 200),

		folder_id: item.media.folder_id || false,
		folder_path: item.media.folder_path || false,
		fileTitle: item.media.name,
		menuConfig: {
			minWidth: 300
		},

		supportsPrefix: true,
		supportsSuffix: true,
		supportsDiff: true,
		supportsUnlink: true,
		supportsRepeatable: true
	});

	delete config.name;

	return config;
});

Phlexible.fields.FieldTypes.addField('folder', {
    titles: {
        de: 'Ordner',
        en: 'Folder'
    },
    iconCls: 'p-frontendmedia-field_folder-icon',
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
        validation: {
            required: 1,
            text: 0,
            numeric: 0,
            content: 0
        }
    }
});
