Phlexible.documenttypes.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.documenttypes.Strings.document_types,
    iconCls: 'p-documenttype-component-icon',
    closable: true,
    layout: 'border',

    initComponent: function() {
        this.items = [{
			xtype: 'documenttypes-documenttypesgrid',
			region: 'center',
			listeners: {
				documenttypeChange: function(r) {
					var mimetypes;
					if (r) {
						mimetypes = r.get('mimetypes');
					} else {
						mimetypes = null;
					}
					this.getComponent(1).loadMimetypes(mimetypes);
				},
				scope: this
			}
		},{
			xtype: 'documenttypes-mimetypesgrid',
			region: 'east',
			width: 400
		}];

        Phlexible.documenttypes.MainPanel.superclass.initComponent.call(this);
    },

    loadParams: function() {

    }
});

Ext.reg('documenttypes-mainpanel', Phlexible.documenttypes.MainPanel);