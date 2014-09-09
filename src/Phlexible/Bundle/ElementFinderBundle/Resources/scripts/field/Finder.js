Ext.reg('twintrigger', Ext.form.TwinTriggerField);

Phlexible.fields.Registry.addFactory('finder', function(parentConfig, item, valueStructure, element, repeatableId) {
	var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, valueStructure, element, repeatableId);

	Ext.apply(config, {
		xtype: 'finderfield',
		hiddenName: config.name,

		width: (parseInt(item.configuration.width, 10) || 200),

        siterootId: element.siteroot_id,
        elementtypeIds: item.configuration.element_type_ids,
        inNavigation: item.configuration.in_navigation,
        maxDepth: item.configuration.max_depth,
        filter: item.configuration.filter,

		supportsPrefix: true,
		supportsSuffix: true,
		supportsDiff: true,
		supportsInlineDiff: true,
		supportsUnlink: {unlinkEl: 'trigger'},
		supportsRepeatable: true
	});

	delete config.name;

	return config;
});

Phlexible.fields.FieldTypes.addField('finder', {
    titles: {
        de: 'Finder',
        en: 'Finder'
    },
    iconCls: 'p-teaser-catch-icon',
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
            readonly: 0,
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
