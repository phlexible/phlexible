Ext.provide('Phlexible.users.GroupsRowTemplate');
Ext.provide('Phlexible.users.GroupsMainPanel');

Ext.require('Phlexible.users.model.Group');

Phlexible.users.GroupsRowTemplate = new Ext.XTemplate(
    '<div style="padding: 10px;">',
    '<div style="font-weight: bold; padding-bottom: 10px;">' + Phlexible.users.Strings.members + ':</div>',
    '<div>',
    '<ul style="list-style-type: disc; padding-left: 25px;">',
    '<tpl for="members">',
    '<li>{.}</li>',
    '</tpl>',
    '</ul>',
    '</div>',
    '</div>'
);

Phlexible.users.GroupsMainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.users.Strings.groups,
    strings: Phlexible.users.Strings,
    layout: 'border',
    iconCls: 'p-user-groups-icon',

    initComponent: function () {
        // Create RowActions Plugin
        var actions = new Ext.ux.grid.RowActions({
            header: this.strings.actions,
            width: 40,
            actions: [
                {
                    iconCls: 'p-user-rename-icon',
                    tooltip: this.strings.rename,
                    callback: this.renameGroup.createDelegate(this),
                    scope: this
                },
                {
                    iconCls: 'p-user-delete-icon',
                    tooltip: this.strings.delete,
                    callback: this.deleteGroup.createDelegate(this),
                    scope: this
                }
            ]
        });

        var expander = new Ext.grid.RowExpander({
            dataIndex: 'members',
            tpl: Phlexible.users.GroupsRowTemplate
        });

        this.items = [
            {
                xtype: 'editorgrid',
                region: 'center',
//            title: this.strings.groups,
//            viewConfig: {
//                forceFit: true
//            },
                //autoExpandColumn: 2,
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('users_groups_list'),
                    autoLoad: true,
                    fields: Phlexible.users.model.Group
                }),
                columns: [
                    expander,
                    {
                        header: this.strings.gid,
                        dataIndex: 'gid',
                        hidden: true,
                        width: 250
                    }, {
                        id: 'group',
                        header: this.strings.group,
                        dataIndex: 'name',
                        editor: new Ext.form.TextField(),
                        sortable: true,
                        width: 300
                    }, {
                        header: this.strings.members,
                        dataIndex: 'memberCnt',
                        sortable: true,
                        width: 100
                    },
                    actions
                ],
                plugins: [
                    expander,
                    actions
                ],
                sm: new Ext.grid.RowSelectionModel({
                    singleSelect: true
                }),
                listeners: {
                    beforeedit: function (e) {
                        if (e.record.data.readonly) {
                            return false;
                        }
                    },
//                rowdblclick: function(grid, rowIndex) {
//                        var r = grid.store.getAt(rowIndex);
//                        this.getComponent(1).store.baseParams = {
//                            role: r.get('id')
//                        };
//                        this.getComponent(1).store.reload();
//                    },
//                    scope: this
                    scope: this
                }
            }
        ];

        this.tbar = [
            {
                text: this.strings.add,
                iconCls: 'p-user-add-icon',
                handler: this.addGroup,
                scope: this
            }
        ];

        Phlexible.users.GroupsMainPanel.superclass.initComponent.call(this);
    },

    addGroup: function () {
        Ext.MessageBox.prompt(this.strings.add, this.strings.add_group, function (btn, name) {
            if (btn != 'ok' || !name) {
                return;
            }
            Ext.Ajax.request({
                url: Phlexible.Router.generate('users_groups_create'),
                method: 'POST',
                params: {
                    name: name
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    if (data.success) {
                        Phlexible.success(data.msg);
                        this.getComponent(0).getStore().reload();
                    } else {
                        Ext.MessageBox.alert('Failure', data.msg);
                    }
                },
                scope: this
            });
        }, this);
    },

    renameGroup: function (grid, record) {
        Ext.MessageBox.prompt(this.strings.rename, String.format(this.strings.rename_group, record.get('name')), function (btn, name) {
            if (btn != 'ok' || !name) {
                return;
            }
            Ext.Ajax.request({
                url: Phlexible.Router.generate('users_groups_rename', {groupId: record.get('gid')}),
                method: 'PATCH',
                params: {
                    name: name
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    if (data.success) {
                        Phlexible.success(data.msg);
                        this.getComponent(0).getStore().reload();
                    } else {
                        Ext.MessageBox.alert('Failure', data.msg);
                    }
                },
                scope: this
            });
        }, this, false, record.get('name'));
    },

    deleteGroup: function (grid, record) {
        Ext.MessageBox.confirm(this.strings.delete, String.format(this.strings.delete_group, record.get('name')), function (btn) {
            if (btn != 'yes') {
                return;
            }
            Ext.Ajax.request({
                url: Phlexible.Router.generate('users_groups_delete', {groupId: record.get('gid')}),
                method: 'DELETE',
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    if (data.success) {
                        Phlexible.success(data.msg);
                        this.getComponent(0).getStore().reload();
                    } else {
                        Ext.MessageBox.alert('Failure', data.msg);
                    }
                },
                scope: this
            });
        }, this);
    }
});

Ext.reg('users-groups-mainpanel', Phlexible.users.GroupsMainPanel);