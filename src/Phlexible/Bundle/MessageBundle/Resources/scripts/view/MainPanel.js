Phlexible.messages.view.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.messages.Strings.view,
    strings: Phlexible.messages.Strings,
    cls: 'p-messages-main-panel',
    iconCls: 'p-message-view-icon',
    layout: 'border',

    initComponent: function(){
        this.items = [{
			xtype: 'messages-view-filterpanel',
			region: 'west',
			width: 200,
			collapsible: true,
			listeners: {
				updateFilter: function(values) {
					this.getComponent(1).store.baseParams.filter = Ext.encode(values);
					this.getComponent(1).store.reload();
					var toolBarObject = this.getComponent(1).getBottomToolbar();
					if ((toolBarObject !== 'undefined') && (typeof toolBarObject.changePage === 'function')) {
						toolBarObject.changePage(1);
					}
				},
				scope: this
			}
		},{
			xtype: 'messages-view-messagesgrid',
			region: 'center',
			listeners: {
				messages: function(data) {
					this.getComponent(0).updateFacets(data.facets);
				},
				scope: this
			}
		}];

        Phlexible.messages.view.MainPanel.superclass.initComponent.call(this);
    }
});

Ext.reg('messages-view-mainpanel', Phlexible.messages.view.MainPanel);
