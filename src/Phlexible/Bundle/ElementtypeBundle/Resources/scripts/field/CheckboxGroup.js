 Phlexible.fields.Registry.addFactory('checkgroup', function(parentConfig, item, valueStructure, pos, element, repeatablePostfix) {
	 if (element.master) {
		 element.prototypes.addFieldPrototype(item);
	 }

	 element.prototypes.incCount(item.dsId);

	 var checkItems = [];
	 if (item.options) {
		 for (var i = 0; i < item.options.length; i++) {
			 checkItems[i] = {
				 name: item.options[i][0],
				 boxLabel: item.options[i][1]
			 };
		 }
	 }
	 var checkColumns = checkItems.length > 4 ? 5 : checkItems.length;

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
		 xtype: 'elementtypes-field-checkboxgroup',
		 name: field_prefix + (repeatablePostfix ? '#' + repeatablePostfix : ''),
		 ds_id: item.dsId,

		 fieldLabel: label,
		 helpText: item.help[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] || '',
		 labelSeparator: labelSeparator,
		 hideLabel: hideLabel,
		 columns: checkColumns,
		 items: checkItems,

		 supportsPrefix: true,
		 supportsSuffix: true,
		 supportsDiff: true,
		 supportsRepeatable: true
	 };

	 if (item.configuration.readonly) {
		 config.readOnly = true;
		 config.ctCls = 'x-item-disabled';
	 }

	 return config;
 });

 Phlexible.fields.FieldTypes.addField('checkgroup', {
    titles: {
        de: 'Checkbox Gruppe',
        en: 'Checkbox group'
    },
    iconCls: 'p-elementtype-field_checkgroup-icon',
    allowedIn: [
		'tab',
		'accordion',
		'group',
		'referenceroot'
	],
	config: {
		labels: {
			field: 1,
			box: 1,
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
		values: {
			default_text: 0,
			default_number: 0,
			default_textarea: 0,
			default_date: 0,
			default_time: 0,
			default_select: 0,
			default_link: 0,
			default_checkbox: 1,
			default_table: 0,
			source: 0,
			source_single: 1,
			source_values: 1,
			source_function: 0,
			source_datasource: 0,
			text: 0
		},
		validation: {
			required: 1,
			text: 0,
			numeric: 0,
			content: 0
		}
	}
});
