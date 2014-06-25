Phlexible.messages.filter.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.messages.Strings.filters,
    strings: Phlexible.messages.Strings,
    iconCls: 'p-message-filter-icon',
    layout: 'border',

    initComponent: function() {
        this.items = [{
			xtype: 'messages-filter-listgrid',
			region: 'west',
			width: '200',
			collapsible: true,
			split: true,
			listeners: {
				filterChange: function(record) {
					if(this.getFilterForm().ready === true){
						this.getFilterForm().ready = false;
						this.getFilterForm().loadData(record);
					}
				},
				filterDeleted: function(record) {
					this.fireEvent('filterDeleted');

					this.getFilterForm().clear();
					this.getFilterForm().disable();
					this.getPreviewPanel().clear();
					this.getPreviewPanel().disable();
				},
				scope: this
			}
		},{
			xtype: 'panel',
			region: 'center',
			layout: 'border',
			border: false,
			items: [{
				xtype: 'messages-filter-criteriaform',
				region: 'west',
				width: '550',
				disabled: true,
				ready: true,
				split: true,
				listeners: {
					reload: function() {
						this.getListGrid().getStore().reload();
					},
					refreshPreview: function(data, title) {
						this.getPreviewPanel().loadData(data, title);
					},
					scope: this
				}
			},{
				xtype: 'messages-filter-previewpanel',
				region: 'center',
				disabled: true
			}]
		}];

        Phlexible.messages.filter.MainPanel.superclass.initComponent.call(this);
    },

	getListGrid: function() {
		return this.getComponent(0);
	},

	getFilterForm: function() {
		return this.getComponent(1).getComponent(0);
	},

	getPreviewPanel: function() {
		return this.getComponent(1).getComponent(1);
	},

    loadParams: function() {
        return;
    }
});

Ext.reg('messages-filter-mainpanel', Phlexible.messages.filter.MainPanel);