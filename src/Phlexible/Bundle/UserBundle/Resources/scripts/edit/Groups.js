Ext.provide('Phlexible.users.edit.Groups');

Ext.require('Phlexible.users.model.UserGroup');
Ext.require('Ext.grid.CheckColumn');

Phlexible.users.edit.Groups = Ext.extend(Ext.grid.GridPanel, {
    strings: Phlexible.users.Strings,
    title: Phlexible.users.Strings.groups,
    iconCls: 'p-user-group-icon',
    border: false,
    stripeRows: true,

    initComponent: function() {
        this.store = new Ext.data.JsonStore({
            autoLoad: true,
            fields: Phlexible.users.model.UserGroup,
            url: Phlexible.Router.generate('users_groups_list'),
            listeners: {
                load: function (store, records) {
                    Ext.each(records, function (record) {
                        if (this.user.get('groups').indexOf(record.get('gid')) !== -1) {
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
            header: this.strings.group,
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

        Phlexible.users.edit.Groups.superclass.initComponent.call(this);
    },

    loadUser: function(user) {
        //this.getStore().proxy.conn.url = Phlexible.Router.generate('users_user_groups', {userId: this.uid});
        this.user = user;
    },

    isValid: function() {
        return true;
    },

    getData: function() {
        var records = this.getStore().getRange(),
            groups = [],
            i;

        for (i = 0; i < records.length; i++) {
            if (records[i].get('member')) {
                groups.push(records[i].get('gid'));
            }
        }

        return {
            groups: groups.join(',')
        };
    }
});

Ext.reg('user_edit_groups', Phlexible.users.edit.Groups);

Phlexible.PluginRegistry.prepend('userEditPanels', {
    xtype: 'user_edit_groups'
});
