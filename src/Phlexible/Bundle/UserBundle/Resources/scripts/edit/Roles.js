Ext.provide('Phlexible.users.edit.Roles');

Ext.require('Phlexible.users.model.UserRole');
Ext.require('Ext.grid.CheckColumn');

Phlexible.users.edit.Roles = Ext.extend(Ext.grid.GridPanel, {
    strings: Phlexible.users.Strings,
    title: Phlexible.users.Strings.roles,
    iconCls: 'p-user-role-icon',
    border: false,
    stripeRows: true,

    initComponent: function() {
        this.store = new Ext.data.JsonStore({
            autoLoad: true,
            fields: Phlexible.users.model.UserRole,
            url: Phlexible.Router.generate('users_roles_list'),
            listeners: {
                load: function (store, records) {
                    Ext.each(records, function (record) {
                        if (this.user.get('roles').indexOf(record.get('id')) !== -1) {
                            record.set('member', 1);
                        }
                    }, this);
                    store.commitChanges();
                },
                scope: this
            }
        });

        this.cc1 = new Ext.grid.CheckColumn({
            header: this.strings.member,
            dataIndex: 'member',
            width: 50
        });

        this.columns = [{
            header: this.strings.role,
            sortable: true,
            dataIndex: 'name',
            width: 300
        },
            this.cc1
        ];

        this.plugins = [this.cc1];

        this.viewCofig = {
            forceFit: true
        };

        Phlexible.users.edit.Roles.superclass.initComponent.call(this);
    },

    loadUser: function(user) {
        //this.getStore().proxy.conn.url = Phlexible.Router.generate('users_user_roles', {userId: this.uid});
        this.user = user;
    },

    isValid: function() {
        return true;
    },

    getData: function() {
        var records = this.getStore().getRange(),
            roles = [],
            i;

        for (i = 0; i < records.length; i++) {
            if (records[i].get('member')) {
                roles.push(records[i].get('id'));
            }
        }

        return {
            roles: roles.join(',')
        };
    }
});

Ext.reg('user_edit_roles', Phlexible.users.edit.Roles);

Phlexible.PluginRegistry.prepend('userEditPanels', {
    xtype: 'user_edit_roles'
});
