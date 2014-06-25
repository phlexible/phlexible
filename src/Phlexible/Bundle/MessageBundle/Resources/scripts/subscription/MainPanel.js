Phlexible.messages.subscription.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.messages.Strings.subscriptions,
    strings: Phlexible.messages.Strings,
    iconCls: 'p-message-subscription-icon',
	layout: 'fit',

    initComponent: function() {

		// Create RowActions Plugin
		var actions = new Ext.ux.grid.RowActions({
			header: this.strings.actions,
			width: 40,
			actions:[{
				iconCls: 'p-message-delete-icon',
				tooltip: this.strings.delete,
				callback: this.deleteSubscription.createDelegate(this),
				scope: this
			}]
		});

		this.items = [{
			xtype: 'grid',
			region: 'center',
			loadMask: true,
			viewConfig: {
				emptyText: this.strings.no_subscriptions,
				deferEmptyText: false
			},
			store: new Ext.data.JsonStore({
				url: Phlexible.Router.generate('messages_subscriptions'),
				fields: Phlexible.messages.model.Subscription,
				id: 'id',
				autoLoad: true
			}),
			columns: [{
				header: this.strings.id,
				dataIndex: 'id',
				width: 200,
				hidden: true
			},{
				header: this.strings.filter,
				width: 200,
				dataIndex: 'filter'
			},{
				header: this.strings.handler,
				width: 200,
				dataIndex: 'handler'
			},
				actions
			],
			plugins: [actions]
		}];

		this.tbar = [{
			xtype: 'combo',
			emptyText: this.strings.filter,
			store: new Ext.data.JsonStore({
				url: Phlexible.Router.generate('messages_filters'),
				id: 'id',
				fields: Phlexible.messages.model.Filter
			}),
			displayField: 'title',
			valueField: 'id',
			mode: 'remote',
			editable: false,
			allowBlank: false,
			triggerAction: 'all'
		}, ' ', {
			xtype: 'iconcombo',
			emptyText: this.strings.handler,
			store: new Ext.data.SimpleStore({
				fields: ['id', 'name', 'iconCls'],
				data: Phlexible.messages.Handlers
			}),
			displayField: 'name',
			valueField: 'id',
			iconClsField: 'iconCls',
			mode: 'local',
			editable: false,
			allowBlank: false,
			triggerAction: 'all'
		}, ' ', {
			xtype: 'button',
			text: this.strings.subscribe,
			iconCls: 'p-message-subscription-icon',
			handler: function() {
				var filter = this.getTopToolbar().items.items[0].getValue(),
					handler = this.getTopToolbar().items.items[2].getValue();

				Ext.Ajax.request({
					url: Phlexible.Router.generate('messages_subscription_create'),
					params: {
						filter: filter,
						handler: handler
					},
					success: function(response) {
						var result = Ext.decode(response.responseText);

						if (result.success) {
							this.getComponent(0).getStore().reload();
							Phlexible.success(result.msg);
						} else {
							Phlexible.failure(result.msg);
						}
					},
					scope: this
				});
			},
			scope: this
		}];

        Phlexible.messages.subscription.MainPanel.superclass.initComponent.call(this);
    },

	reloadSubscriptions: function() {
		this.getComponent(0).getStore().reload();
	},

	deleteSubscription: function(grid, record) {
		Ext.Ajax.request({
			url: Phlexible.Router.generate('messages_subscription_delete', {id: record.data.id}),
			success: function(response) {
				var result = Ext.decode(response.responseText);

				if (result.success) {
					this.getComponent(0).getStore().reload();
					Phlexible.success(result.msg);
				} else {
					Phlexible.failure(result.msg);
				}
			},
			scope: this
		})
	}
});

Ext.reg('messages-subscription-mainpanel', Phlexible.messages.subscription.MainPanel);