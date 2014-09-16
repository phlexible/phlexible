Phlexible.elements.LocksWindow = Ext.extend(Ext.Window, {
    title: Phlexible.elements.Strings.lock.locks,
    strings: Phlexible.elements.Strings.lock,
    width: 660,
    height: 300,
    iconCls: 'p-element-lock-icon',
    layout: 'fit',
    maximizable: true,

    initComponent: function () {
        this.items = [
            new Ext.grid.GridPanel({
                border: false,
                viewConfig: {
                    emptyText: this.strings.no_locked_items
                },
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('locks_list'),
                    root: 'locks',
                    fields: ['id', 'uid', 'user', 'ts', 'eid', 'lock_type'],
                    id: 'id',
                    autoLoad: true,
                    sortInfo: {
                        field: 'user',
                        direction: 'asc'
                    }
                }),
                sm: new Ext.grid.RowSelectionModel({
                    listeners: {
                        selectionchange: function (sm) {
                            var records = sm.getSelections();
                            if (!records) {
                                this.getTopToolbar().items.items[0].disable();
                            } else {
                                this.getTopToolbar().items.items[0].enable();
                            }
                        },
                        scope: this
                    }
                }),
                columns: [
                    {
                        header: 'ID',
                        dataIndex: 'id',
                        width: 300,
                        sortable: true,
                        hidden: true
                    },
                    {
                        header: 'UID',
                        dataIndex: 'uid',
                        width: 100,
                        sortable: true,
                        hidden: true
                    },
                    {
                        header: 'User',
                        dataIndex: 'user',
                        width: 130,
                        sortable: true
                    },
                    {
                        header: 'Time',
                        dataIndex: 'ts',
                        width: 130,
                        sortable: true
                    },
                    {
                        header: 'EID',
                        dataIndex: 'eid',
                        width: 100,
                        sortable: true
                    },
                    {
                        header: 'Lock Type',
                        dataIndex: 'lock_type',
                        width: 130,
                        sortable: true
                    }
                ]
            })
        ];

        this.tbar = [
            {
                text: this.strings.remove_lock,
                disabled: true,
                handler: function () {
                    var r = this.getComponent(0).getSelectionModel().getSelected();

                    Ext.Ajax.request({
                        url: Phlexible.Router.generate('locks_delete'),
                        params: {
                            id: r.id,
                            lock_type: r.data.lock_type
                        },
                        success: function (response) {
                            var data = Ext.decode(response.responseText);

                            if (data.success) {
                                Phlexible.msg('Success', data.message);

                                this.getComponent(0).store.reload();
                            } else {
                                Ext.MessageBox.alert('Failure', data.message);
                            }
                        },
                        scope: this
                    });
                },
                scope: this
            },
            '-',
            {
                text: this.strings.remove_my_locks,
                handler: function () {
                    Ext.Ajax.request({
                        url: Phlexible.Router.generate('locks_delete_my'),
                        success: function (response) {
                            var data = Ext.decode(response.responseText);

                            if (data.success) {
                                Phlexible.msg('Success', data.message);

                                this.getComponent(0).store.reload();
                            } else {
                                Ext.MessageBox.alert('Failure', data.message);
                            }
                        },
                        scope: this
                    });
                },
                scope: this
            },
            '->',
            {
                text: this.strings.remove_all_locks,
                handler: function () {
                    Ext.Ajax.request({
                        url: Phlexible.Router.generate('locks_flush'),
                        success: function (response) {
                            var data = Ext.decode(response.responseText);

                            if (data.success) {
                                Phlexible.msg('Success', data.message);

                                this.getComponent(0).store.reload();
                            } else {
                                Ext.MessageBox.alert('Failure', data.message);
                            }
                        },
                        scope: this
                    });
                },
                scope: this
            }
        ];

        Phlexible.elements.LocksWindow.superclass.initComponent.call(this);
    }
});
