/**
 * Input params:
 * - uid (optional)
 *   Set focus on specific user
 */
Phlexible.users.UsersMainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.users.Strings.users,
    layout: 'border',
    cls: 'p-users-main-panel',
    iconCls: 'p-user-users-icon',

    initComponent: function () {
        this.items = [
            {
                xtype: 'users-users-filterpanel',
                region: 'west',
                width: 200,
                collapsible: true,
                params: this.params,
                listeners: {
                    applySearch: function (values) {
                        this.getComponent(1).store.baseParams.search = Ext.encode(values);
                        this.getComponent(1).store.reload();
                    },
                    scope: this
                }
            },
            {
                xtype: 'users-users-grid',
                region: 'center',
                params: this.params
            }
        ];

        Phlexible.users.UsersMainPanel.superclass.initComponent.call(this);
    },

    loadParams: function (params) {
        var uid = params.userId;

        var r = this.getComponent(1).store.getById(uid);
        if (r) {
            this.getComponent(1).getSelectionModel().selectRecords([r]);
        }
    }
});

Ext.reg('users-users-mainpanel', Phlexible.users.UsersMainPanel);