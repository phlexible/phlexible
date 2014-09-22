Phlexible.fields.Registry.addFactory('form', function(parentConfig, item, valueStructure, element, repeatableId) {
	var store = new Ext.data.JsonStore({
        url: Phlexible.Router.generate('form_list'),
		fields: ['name'],
        id: 'form',
        autoLoad: true
	});

	var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, valueStructure, element, repeatableId);

	Ext.apply(config, {
		xtype: 'twincombobox',
		hiddenName: config.name,

		listWidth: (parseInt(item.configuration.width, 10) || 200),

		store: store,
		valueField: 'name',
		displayField: 'name',
		mode: 'remote',
		typeAhead: false,
		editable: false,
		triggerAction: 'all',
		selectOnFocus: true,
		hideMode: 'offsets',

		supportsPrefix: true,
		supportsSuffix: true,
		supportsDiff: true,
		supportsInlineDiff: true,
		supportsUnlink: {unlinkEl: 'trigger'},
		supportsRepeatable: true
	});

	if (config.readOnly) {
		config.editable = false;
		config.hideTrigger1 = true;
		config.hideTrigger2 = true;
		config.onTrigger1Click = Ext.emptyFn;
		config.onTrigger2Click = Ext.emptyFn;
	}

	delete config.name;

	return config;
});

Phlexible.fields.FieldTypes.addField('form', {
    titles: {
        de: 'Formular',
        en: 'Form'
    },
    iconCls: 'p-form-form-icon',
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
