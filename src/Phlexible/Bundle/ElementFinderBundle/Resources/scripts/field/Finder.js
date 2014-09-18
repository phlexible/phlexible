Phlexible.fields.Registry.addFactory('finder', function(parentConfig, item, valueStructure, element, repeatableId) {
	var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, valueStructure, element, repeatableId);

	Ext.apply(config, {
		xtype: 'finderfield',
		hiddenName: config.name,

		width: (parseInt(item.configuration.width, 10) || 200),

        siterootId: element.siteroot_id,

		supportsPrefix: true,
		supportsSuffix: true,
		supportsDiff: true,
		supportsInlineDiff: true,
		supportsUnlink: {unlinkEl: 'trigger'},
		supportsRepeatable: true
	});

    config.baseValues = {
        elementtypeIds: item.configuration.element_type_ids,
        inNavigation: item.configuration.in_navigation,
        maxDepth: item.configuration.max_depth,
        sortField: item.configuration.sort_field,
        sortDir: item.configuration.sort_dir,
        filter: item.configuration.filter,
        template: item.configuration.template
    };
    if (config.value) {
        config.hiddenValue = config.value;
        config.value = 'configured';
    }

	delete config.name;

	return config;
});

Phlexible.fields.FieldTypes.addField('finder', {
    titles: {
        de: 'Finder',
        en: 'Finder'
    },
    iconCls: 'p-elementfinder-finder-icon',
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
