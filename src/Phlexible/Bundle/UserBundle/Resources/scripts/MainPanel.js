/**
 * Input params:
 * - uid (optional)
 *   Set focus on specific user
 */
Phlexible.users.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.users.Strings.users,
    layout: 'fit',
    cls: 'p-users-main-panel',
    iconCls: 'p-user-component-icon',

    initComponent: function () {
        this.items = {
            xtype: 'tabpanel',
            border: false,
            activeTab: 0,
            items: [
                {
                    xtype: 'users-users-mainpanel',
                    params: this.params
                },
                {
                    xtype: 'users-groups-mainpanel'
                }
            ]
        };

        Phlexible.users.UsersMainPanel.superclass.initComponent.call(this);
    },

    loadParams: function (params) {
        this.getComponent(0).getComponent(0).loadParams(params);
    }
});
Ext.reg('users-main', Phlexible.users.MainPanel);