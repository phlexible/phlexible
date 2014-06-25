Ext.Editor.prototype.beforeDestroy = function(){
    if(this.field) this.field.destroy();
    this.field = null;
};

Phlexible.fields.Registry.addFactory('table', function(parentConfig, item, valueStructure, pos, element, repeatablePostfix, forceAdd) {
	if (element.master) {
		element.prototypes.addFieldPrototype(item);
	}

	element.prototypes.incCount(item.dsId);

	var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, element, repeatablePostfix, forceAdd);

	Ext.apply(config, {
		xtype: 'tablefield',
		width: (parseInt(item.configuration.width, 10) || 400),
		height: (parseInt(item.configuration.height, 10) || 200),
		value: item.content,
		tableDefaultCols: 2,

		addRowAboveText: Phlexible.elementtypes.Strings.add_row_above,
		addRowBelowText: Phlexible.elementtypes.Strings.add_row_below,
		deleteRowText: Phlexible.elementtypes.Strings.delete_row,
		addColumnBeforeText: Phlexible.elementtypes.Strings.add_column_before,
		addColumnAfterText: Phlexible.elementtypes.Strings.add_column_after,
		deleteColumnText: Phlexible.elementtypes.Strings.delete_column,
		headersText: Phlexible.elementtypes.Strings.headers,
		firstRowIsHeaderText: Phlexible.elementtypes.Strings.first_row_is_header,
		firstColumnIsHeaderText: Phlexible.elementtypes.Strings.first_column_is_header,

		supportsDiff: true
	});

	return config;
});

Phlexible.fields.FieldTypes.addField('table', {
    titles: {
        de: 'Tabelle',
        en: 'Table'
    },
    iconCls: 'p-elementtype-field_table-icon',
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
            height: 1,
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
