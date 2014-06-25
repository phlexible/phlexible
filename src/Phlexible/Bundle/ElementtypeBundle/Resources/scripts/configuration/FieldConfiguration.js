Phlexible.elementtypes.configuration.FieldConfiguration = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.configuration,
    iconCls: 'p-elementtype-tab_configuration-icon',
    border: false,
	autoScroll: true,
    bodyStyle: 'padding:3px',
    defaultType: 'textfield',
    labelWidth: 150,

    initComponent: function() {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('elementtypes_list', {type: 'full'}),
            success: function(response) {
                var data = Ext.decode(response.responseText);

                this.linkElementtypesStore.loadData(data.elementtypes);
            },
            scope: this
        });

		this.initMyItems();

        this.linkElementtypesStore = new Ext.data.JsonStore({
            fields: ['id', 'title'],
            id: 'id'
        });

        Phlexible.elementtypes.configuration.FieldConfiguration.superclass.initComponent.call(this);
    },

	initMyItems: function() {
		this.items = [{
			// 0
			xtype: 'combo',
			fieldLabel: this.strings.language,
			hiddenName: 'synchronized',
			store: new Ext.data.SimpleStore({
				fields: ['key', 'title'],
				data: [
					['no', this.strings.not_synchronized],
					['synchronized', this.strings['synchronized']],
					['synchronized_unlink', this.strings.synchronized_with_unlink]
				]
			}),
			width: 183,
			listWidth: 200,
			displayField: 'title',
			valueField: 'key',
			editable: false,
			mode: 'local',
			typeAhead: false,
			triggerAction: 'all'
		},{
			// 1
			xtype: 'numberfield',
			fieldLabel: this.strings.width,
			name: 'width',
			width: 200,
			allowDecimals: false,
			allowNegative: false,
			minValue: 20
		},{
			// 2
			xtype: 'numberfield',
			fieldLabel: this.strings.height,
			name: 'height',
			width: 200,
			allowDecimals: false,
			allowNegative: false,
			minValue: 20
		},{
			// 3
			xtype: 'checkbox',
			name: 'readonly',
			fieldLabel: '',
			labelSeparator: '',
			boxLabel: this.strings.readonly
		},{
			// 4
			xtype: 'checkbox',
			name: 'hide_label',
			fieldLabel: '',
			labelSeparator: '',
			boxLabel: this.strings.hide_label
		},{
			//5
			xtype: 'checkbox',
			name: 'sortable',
			fieldLabel: this.strings.child_items,
			boxLabel: this.strings.can_be_sorted
		},{
			xtype: 'elementtypes-configuration-field-configuration-accordion',
			additional: true
		},{
			xtype: 'elementtypes-configuration-field-configuration-group',
			additional: true
		},{
			xtype: 'elementtypes-configuration-field-configuration-link',
			additional: true
		},{
			xtype: 'elementtypes-configuration-field-configuration-table',
			additional: true
		}];
	},

    updateVisibility: function(fieldType, type) {
        // language sync
        if (fieldType.config.configuration.sync) {
            this.getComponent(0).show();
        }
        else {
            this.getComponent(0).hide();
        }

        // width
        if (fieldType.config.configuration.width) {
            this.getComponent(1).show();
        }
        else {
            this.getComponent(1).hide();
        }

        // height
        if (fieldType.config.configuration.height) {
            this.getComponent(2).show();
        }
        else {
            this.getComponent(2).hide();
        }

        // readonly
        if (fieldType.config.configuration.readonly) {
            this.getComponent(3).show();
        }
        else {
            this.getComponent(3).hide();
        }

        // hide label
        if (fieldType.config.configuration.hide_label) {
            this.getComponent(4).show();
        }
        else {
            this.getComponent(4).hide();
        }

        // children sortable
        if (fieldType.config.configuration.sortable) {
            this.getComponent(5).show();
        }
        else {
            this.getComponent(5).hide();
        }

		this.items.each(function(panel) {
			if (panel.additional) {
				panel.updateVisibility(type);
			}
		});
	},

    loadData: function(fieldData, fieldType, type) {
        this.updateVisibility(fieldType, type);

        this.getForm().setValues([
            {id: 'synchronized',        value: fieldData['synchronized']},
            {id: 'width',               value: fieldData.width},
            {id: 'height',              value: fieldData.height},
            {id: 'sortable',            value: fieldData.sortable},
            {id: 'readonly',            value: fieldData.readonly},
            {id: 'hide_label',          value: fieldData.hide_label}
        ]);

		this.items.each(function(panel) {
			if (panel.additional) {
				panel.loadData(fieldData, fieldType);
			}
		});

        this.isValid();
    },

    getSaveValues: function() {
        var data = this.getForm().getValues();

		this.items.each(function(panel) {
			if (panel.additional && !panel.hidden) {
				Ext.apply(data, panel.getSaveValues());
			}
		});

        return data;
    },

    isValid: function() {
		var valid = this.getForm().isValid();

		if (valid) {
			this.items.each(function(panel) {
				if (panel.additional) {
					valid = valid && panel.isValid();
				}
			});
		}

		if (valid) {
            //this.header.child('span').removeClass('error');
            this.setIconClass('p-elementtype-tab_configuration-icon');

            return true;
        } else {
            //this.header.child('span').addClass('error');
            this.setIconClass('p-elementtype-tab_error-icon');

            return false;
        }
    },

    loadField: function(properties, node, fieldType) {
        if (fieldType.config.configuration) {
            this.ownerCt.getTabEl(this).hidden = false;
            this.loadData(properties.configuration, fieldType, node.attributes.type);
        }
        else {
            this.ownerCt.getTabEl(this).hidden = true;
        }
    }
});

Ext.reg('elementtypes-configuration-field-configuration', Phlexible.elementtypes.configuration.FieldConfiguration);