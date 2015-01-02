Ext.ns('Phlexible.messages');

Phlexible.messages.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.messages.Strings.messages,
    iconCls: 'p-message-component-icon',
    layout: 'fit',

    initComponent: function () {
        var mainItems = [
            {
                xtype: 'messages-view-mainpanel'
            }
        ];

        if (Phlexible.User.isGranted('ROLE_MESSAGE_FILTERS')) {
            mainItems.push({
                xtype: 'messages-filter-mainpanel',
                listeners: {
                    filterDeleted: function () {
                        if (Phlexible.User.isGranted('ROLE_MESSAGE_SUBSCRIPTIONS')) {
                            this.getComponent(0).getComponent(2).reloadSubscriptions();
                        }
                    },
                    scope: this
                }
            });
        }
        if (Phlexible.User.isGranted('ROLE_MESSAGE_SUBSCRIPTIONS')) {
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