Ext.provide('Phlexible.users.UserGrid');

Ext.require('Phlexible.users.model.User');
Ext.require('Phlexible.users.SuccessorWindow');
Ext.require('Phlexible.users.UserWindow');

Phlexible.users.UserGrid = Ext.extend(Ext.grid.GridPanel, {
    title: Phlexible.users.Strings.users,
    strings: Phlexible.users.Strings,
    stripeRows: true,
    loadMask: true,
    viewConfig: {
        emptyText: Phlexible.users.Strings.no_users,
        deferEmptyText: false
    },

    initComponent: function () {
        this.store = new Ext.data.JsonStore({
            root: 'users',
            baseParams: {
                limit: 20
            },
            id: 'uid',
            totalProperty: 'count',
            fields: Phlexible.users.model.User,
            url: Phlexible.Router.generate('users_users_list'),
            listeners: {
                load: function () {
                    this.getTopToolbar().items.items[1].disable();
                },
                scope: this
            },
            remoteSort: true,
            sortInfo: {
                field: "username",
                direction: "ASC"
            }
        });

        this.tbar = [
            {
                id: 'addBtn',
                text: this.strings.new_user,
                iconCls: 'p-user-add-icon',
                handler: this.addUser,
                scope: this
            },
            {
                id: 'deleteBtn',
                text: this.strings['delete'],
                iconCls: 'p-user-delete-icon',
                handler: this.deleteUser,
                scope: this,
                disabled: true
            }
        ];

        if (Phlexible.User.isGranted('ROLE_SWITCH_USER')) {
            this.tbar.push('-');
            this.tbar.push({
                text: this.strings.impersonate,
                iconCls: 'p-user-user_impersonate-icon',
                handler: this.impersonateUser,
                scope: this,
                disabled: true
            });
        }

        if (Phlexible.User.isGranted('ROLE_SUPER_ADMIN')) {
            this.tbar.push('-');
            this.tbar.push({
                id: 'successorBtn',
                text: this.strings.successor,
                iconCls: 'p-user-user_successor-icon',
                handler: this.showSuccessorWindow,
                scope: this,
                disabled: true
            });
        }

        this.columns = [
            {
                header: this.strings.uid,
                sortable: true,
                dataIndex: 'uid',
                hidden: true,
                width: 250
            },
            {
                header: this.strings.username,
                sortable: true,
                dataIndex: 'username',
                width: 100
            },
            {
                header: this.strings.email,
                sortable: true,
                dataIndex: 'email',
                width: 200
            },
            {
                header: this.strings.firstname,
                sortable: true,
                dataIndex: 'firstname',
                width: 100
            },
            {
                header: this.strings.lastname,
                sortable: true,
                dataIndex: 'lastname',
                width: 100
            },
            {
                header: this.strings.comment,
                sortable: true,
                dataIndex: 'comment',
                width: 200
            },
            {
                header: this.strings.expireDate,
                sortable: true,
                dataIndex: 'expireDate',
                hidden: true,
                width: 100
            },
            {
                header: this.strings.createDate,
                sortable: true,
                dataIndex: 'createDate',
                hidden: true,
                width: 100
            },
            {
                header: this.strings.createUser,
                sortable: true,
                dataIndex: 'createUser',
                hidden: true,
                width: 100
            },
            {
                header: this.strings.modifyDate,
                sortable: true,
                dataIndex: 'modifyDate',
                hidden: true,
                width: 100
            },
            {
                header: this.strings.modifyUser,
                sortable: true,
                dataIndex: 'modifyUser',
                hidden: true,
                width: 100
            }
        ];

        this.selModel = new Ext.grid.RowSelectionModel({
            singleSelect: true,
            listeners: {
                selectionchange: function (sm) {
                    var s = sm.getSelections();

                    var tb = this.getTopToolbar();
                    if (s.length === 0) {
                        tb.items.each(function (item) {
                            item.disable();
                        });
                        tb.items.items[0].enable();
                    } else if (s.length === 1) {
                        tb.items.each(function (item) {
                            item.enable();
                        });
                        tb.items.items[0].enable();
                    } else {
                        tb.items.each(function (item) {
                            item.disable();
                        });
                        tb.items.items[0].enable();
                        tb.items.items[1].enable();
                    }

                },
                scope: this
            }
        });

        this.bbar = new Ext.PagingToolbar({
            pageSize: 20,
            store: this.store,
            displayInfo: true,
            displayMsg: this.strings.display_msg,
            emptyMsg: this.strings.empty_msg
        });

        this.addListener({
            rowdblclick: function (grid, rowIndex) {
                var record = grid.store.getAt(rowIndex);
                var w = new Phlexible.users.UserWindow({
                    listeners: {
                        save: function () {
                            this.store.reload();
                        },
                        scope: this
                    }
                });
                w.show(record);
            }
        });

        Phlexible.users.UserGrid.superclass.initComponent.call(this);
    },

    addUser: function () {
        var defaults = Phlexible.Config.get('defaults'),
            record = new Phlexible.users.model.User({
                uid: '',
                username: '',
                firstname: '',
                lastname: '',
                email: '',
                options: {
                    interfaceLanguage: defaults.language,
                    theme: defaults.theme
                },
                account: {
                    forcePasswordChange: defaults.force_password_change,
                    noPasswordChange: defaults.cant_change_password,
                    noPasswordExpire: defaults.password_doesnt_expire
                },
                roles: [
                    'user'
                ],
                groups: [
                ]
            });

        var w = new Phlexible.users.UserWindow({
            mode: 'add',
            listeners: {
                save: function () {
                    this.store.reload();
                },
                scope: this
            }
        });

        w.show(record);
    },

    remove: function () {
        Ext.Msg.alert(uid);
    },

    showSuccessorWindow: function () {
        var selectionModel = this.getSelectionModel();
        var records = selectionModel.getSelections();

        if (records.length !== 1) {
            Ext.MessageBox.alert('Failure', 'Set successor only works for one user at a time.');
            return;
        }

        var uid = records[0].id;
        var w = new Phlexible.users.SuccessorWindow({
            userId: uid,
            listeners: {
                submit: function(values) {
                    this.onSetSuccessor(uid, values.successor);
                },
                scope: this
            }
        });
        w.show();
    },

    onSetSuccessor: function(uid, successor) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('users_successor_set', {userId: uid}),
            method: 'POST',
            params: {
                successor: successor
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);
                if (data.success) {
                    this.store.reload();
                } else {
                    Ext.Msg.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    },

    deleteUser: function () {
        var selectionModel = this.getSelectionModel();
        var records = selectionModel.getSelections();
        var msg;
        if (records.length > 1) {
            msg = this.strings.delete_users_warning;
        } else {
            msg = String.format(this.strings.delete_user_warning, records[0].get('username'));
        }

        var uids = [];
        for (var i = 0; i < records.length; i++) {
            uids.push(records[i].get('uid'));
        }

        var w = new Phlexible.users.SuccessorWindow({
            uids: uids,
            listeners: {
                submit: function (values) {
                    this.onDeleteUser(uids, values.successor);
                },
                scope: this
            }
        });
        w.show();
    },

    onDeleteUser: function (uids, successor) {
        if (!uids || !successor) {
            return;
        }

        Ext.each(uids, function (uid) {
            Ext.Ajax.request({
                url: Phlexible.Router.generate('users_users_delete', {userId: uid}),
                method: 'DELETE',
                params: {
                    successor: successor
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);
                    if (data.success) {
                        this.store.reload();
                    } else {
                        Ext.Msg.alert('Failure', data.msg);
                    }
                },
                scope: this
            });
        }, this);
    },

    impersonateUser: function () {
        var selectionModel = this.getSelectionModel();
        var r = selectionModel.getSelected();

        document.location.href = Phlexible.Router.generate('gui_index', {_switch_user: r.data.username});
    }
});

Ext.reg('users-users-grid', Phlexible.users.UserGrid);