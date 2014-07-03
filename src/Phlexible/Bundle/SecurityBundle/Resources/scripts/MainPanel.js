Phlexible.security.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.security.Strings.roles,
    strings: Phlexible.security.Strings,
    layout: 'border',
    iconCls: 'p-security-roles-icon',

    initComponent: function () {
        // Create RowActions Plugin
        var actions = new Ext.ux.grid.RowActions({
            header: this.strings.actions,
            width: 40,
            actions: [
                {
                    iconCls: 'p-security-rename-icon',
                    tooltip: this.strings.rename,
                    hideIndex: 'type',
                    callback: this.renameRole.createDelegate(this),
                    scope: this
                },
                {
                    iconCls: 'p-security-delete-icon',
                    tooltip: this.strings.delete,
                    hideIndex: 'type',
                    callback: this.deleteRole.createDelegate(this),
                    scope: this
                }
            ]
        });

        this.items = [
            {
                xtype: 'grid',
                region: 'west',
                title: this.strings.roles,
                width: 300,
                stripeRows: true,
                autoExpandColumn: 1,
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('security_acl_roles'),
                    autoLoad: true,
                    fields: Phlexible.security.model.Role,
                    sortInfo: {
                        field: 'name',
                        direction: 'ASC'
                    }
                }),
                columns: [
                    {
                        dataIndex: 'type',
                        width: 20,
                        renderer: function (s) {
                            if (s == 'locked') {
                                return Phlexible.inlineIcon('p-security-type_locked-icon');
                            } else if (s == 'editable') {
                                return Phlexible.inlineIcon('p-security-type_editable-icon');
                            }

                            return '';
                        }
                    },
                    {
                        header: this.strings.role,
                        dataIndex: 'name',
                        width: 200
                    },
                    actions
                ],
                sm: new Ext.grid.RowSelectionModel({
                    singleSelect: true
                }),
                plugins: [actions],
                tbar: [
                    {
                        text: this.strings.add,
                        iconCls: 'p-security-add-icon',
                        handler: this.addRole,
                        scope: this
                    }
                ],
                listeners: {
                    rowdblclick: function (grid, rowIndex) {
                        var r = grid.store.getAt(rowIndex);
                        if (r.data.type == 'locked') {
                            this.getComponent(1).getTopToolbar().items.items[0].disable();
                            this.getComponent(1).getTopToolbar().items.items[1].disable();
                        } else {
                            this.getComponent(1).getTopToolbar().items.items[0].enable();
                            this.getComponent(1).getTopToolbar().items.items[1].enable();
                        }
                        this.getComponent(1).setTitle(String.format(this.strings.resources_for_role, r.get('id')));
                        this.getComponent(1).role = r.get('id');
                        this.getComponent(1).getStore().proxy.conn.url = Phlexible.Router.generate('security_acl_role_resources', {role: r.get('id')});
                        this.getComponent(1).store.reload({
                            callback: function () {
                                this.getComponent(1).enable();
                            },
                            scope: this
                        });
                    },
                    scope: this
                }
            },
            {
                xtype: 'grid',
                region: 'center',
                title: this.strings.resources,
                disabled: true,
                loadMask: true,
                stripeRows: true,
                store: new Ext.data.JsonStore({
                    url: '',
                    params: {
                        role: 'user'
                    },
                    //autoLoad: true,
                    fields: Phlexible.security.model.RoleResource,
                    sortInfo: {
                        field: 'name',
                        direction: 'ASC'
                    }
                }),
                columns: [
                    {
                        header: this.strings.resource,
                        dataIndex: 'name',
                        width: 150
                    },
                    this.f1 = new Ext.grid.CheckColumn({header: this.strings.full, dataIndex: 'allowed', width: 80})
//                this.f2 = new Ext.grid.CheckColumn({header: this.strings.read, dataIndex: 'allowed', width: 80}),
//                this.f3 = new Ext.grid.CheckColumn({header: this.strings.write, dataIndex: 'allowed', width: 80})
                ],
                plugins: [
                    this.f1
//                this.f2,
//                this.f3
                ],
                tbar: [
                    {
                        text: this.strings.save,
                        iconCls: 'p-security-save-icon',
                        handler: this.saveRole,
                        scope: this
                    },
                    {
                        text: this.strings.reset,
                        iconCls: 'p-security-reset-icon',
                        handler: function () {
                            this.getComponent(1).store.reload();
                        },
                        scope: this
                    }
                ]
            }
        ];

        Phlexible.security.MainPanel.superclass.initComponent.call(this);
    },

    addRole: function () {
        Ext.MessageBox.prompt(this.strings.add, this.strings.add_role, function (btn, role) {
            if (btn != 'ok' || !role) {
                return;
            }
            Ext.Ajax.request({
                url: Phlexible.Router.generate('security_acl_create'),
                method: 'POST',
                params: {
                    role: role
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    if (data.success) {
                        Phlexible.success(data.msg);
                        this.getComponent(0).store.reload();
                    } else {
                        Ext.MessageBox.alert('Failure', data.msg);
                    }
                },
                scope: this
            });
        }, this);
    },

    renameRole: function (grid, record) {
        Ext.MessageBox.prompt(this.strings.rename, String.format(this.strings.rename_role, record.get('name')), function (btn, name) {
            if (btn != 'ok' || !name) {
                return;
            }
            Ext.Ajax.request({
                url: Phlexible.Router.generate('security_acl_rename', {role: record.get('id')}),
                method: 'PATCH',
                params: {
                    name: name
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    if (data.success) {
                        Phlexible.success(data.msg);
                        this.getComponent(0).store.reload();
                    } else {
                        Ext.MessageBox.alert('Failure', data.msg);
                    }
                },
                scope: this
            });
        }, this, false, record.get('name'));
    },

    deleteRole: function (grid, record) {
        Ext.MessageBox.confirm(this.strings['delete'], String.format(this.strings.delete_role, record.get('name')), function (btn) {
            if (btn != 'yes') {
                return;
            }
            Ext.Ajax.request({
                url: Phlexible.Router.generate('security_acl_delete', {role: record.get('id')}),
                method: 'DELETE',
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    if (data.success) {
                        Phlexible.success(data.msg);
                        this.getComponent(0).store.reload();
                        this.getComponent(1).store.removeAll();
                        this.getComponent(1).setTitle(this.strings.resources);
                        this.getComponent(1).disable();
                    } else {
                        Ext.MessageBox.alert('Failure', data.msg);
                    }
                },
                scope: this
            });
        }, this);
    },

    saveRole: function () {
        var records = this.getComponent(1).store.getRange();
        var data = [];

        for (var i = 0; i < records.length; i++) {
            if (records[i].data.allowed) {
                data.push(records[i].data.id);
            }
        }

        if (!data.length) return;

        Ext.Ajax.request({
            url: Phlexible.Router.generate('security_acl_save', {role: this.getComponent(1).role}),
            method: 'PUT',
            params: {
                resources: data.join(',')
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    Phlexible.success(data.msg);
                    this.getComponent(1).store.commitChanges();
                    this.getComponent(0).store.reload();
                } else {
                    Ext.MessageBox.alert('Error', data.msg);
                }
            },
            scope: this
        });
    }
});
Ext.reg('security-roles', Phlexible.security.MainPanel);