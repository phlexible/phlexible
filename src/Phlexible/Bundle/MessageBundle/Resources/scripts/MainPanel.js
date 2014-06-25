Phlexible.messages.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.messages.Strings.messages,
    iconCls: 'p-message-component-icon',
    layout: 'fit',

    initComponent: function() {
        var mainItems = [{
            xtype: 'messages-view-mainpanel'
        }];

        if (Phlexible.User.isGranted('messages_filters')) {
            mainItems.push({
                xtype: 'messages-filter-mainpanel',
				listeners: {
					filterDeleted: function() {
						if (Phlexible.User.isGranted('messages_subscriptions')) {
							this.getComponent(0).getComponent(2).reloadSubscriptions();
						}
					},
					scope: this
				}
            });
        }
        if (Phlexible.User.isGranted('messages_subscriptions')) {
            mainItems.push({
                xtype: 'messages-subscription-mainpanel'
            });
        }

        this.items = {
            xtype: 'tabpanel',
            deferredRender: true,
            activeItem: 0,
            border: false,
            items: mainItems
        };

        Phlexible.messages.MainPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('messages-mainpanel', Phlexible.messages.MainPanel);