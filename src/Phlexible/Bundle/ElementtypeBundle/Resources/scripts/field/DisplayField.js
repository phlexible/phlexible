Phlexible.fields.Registry.addFactory('displayfield', function(parentConfig, item, valueStructure, pos, element, repeatablePostfix) {
	if (element.master) {
		element.prototypes.addFieldPrototype(item);
	}

	element.prototypes.incCount(item.dsId);

	// labels
	var hideLabel;
	var label;
	var labelSeparator = ':';
	if (parentConfig.singleLineLabel) {
		label = parentConfig.singleLineLabel;
		parentConfig.singleLineLabel = '';
		hideLabel = false;
	} else if(parentConfig.singleLine) {
		hideLabel = true;
		label = item.labels.fieldlabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')];
	} else if(item.configuration.hide_label) {
		hideLabel = false;
		label = '';
		labelSeparator = '';
	} else {
		hideLabel = false;
		label = item.labels.fieldlabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')];
	}

	var field_prefix = 'field_' + item.dsId + '_';
	if(item.data_id) {
		field_prefix += 'id-' + item.data_id;
	} else {
		field_prefix += Ext.id(null, 'new');
	}

	var config = {
		xtype: 'displayfield',
		name: field_prefix + (repeatablePostfix ? '#' + repeatablePostfix : ''),
		ds_id: item.dsId,

		fieldLabel: label,
		helpText: (item.labels.context_help[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] || ''),
		prefix: (item.labels.prefix[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] || ''),
		suffix: (item.labels.suffix[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] || ''),
		labelSeparator: labelSeparator,
		hideLabel: hideLabel,
		value: item.content,
		width: (item.configuration.width || 100),
		element: element,

		supportsPrefix: true,
		supportsSuffix: true
	};

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
            sync: 0,
            width: 1,
            height: 0,
            readonly: 0,
            hide_label: 1,
            sortable: 0
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
