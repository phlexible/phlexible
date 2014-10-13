Phlexible.tasks.TasksGrid = Ext.extend(Ext.grid.GridPanel, {
    strings: Phlexible.tasks.Strings,
    cls: 'p-tasks-task-grid',
    viewConfig: {
        deferEmptyText: false,
        emptyText: Phlexible.tasks.Strings.no_tasks_found
    },
    autoExpandColumn: 6,
    loadMask: true,

    initComponent: function () {
        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('tasks_list'),
            baseParams: {
                start: 0,
                limit: 20
            },
            fields: Phlexible.tasks.model.Task,
            root: 'tasks',
            id: 'id',
            totalProperty: 'total',
            remoteSort: true,
            listeners: {
                load: function () {
                    if (this.taskId) {
                        var row = this.getStore().find('id', this.taskId);
                        if (row !== -1) {
                            this.getSelectionModel().selectRow(row);
                        }
                        this.taskId = false;
                    }
                },
                scope: this
            }
        });

        this.columns = [
            {
                header: this.strings.id,
                dataIndex: 'id',
                width: 220,
                hidden: true
            }, {
                header: this.strings.type,
                dataIndex: 'type',
                width: 200,
                hidden: true
            }, {
                header: this.strings.component,
                dataIndex: 'component',
                width: 100,
                hidden: true
            }, {
                header: this.strings.status,
                dataIndex: 'status',
                width: 120,
                renderer: function (s) {
                    return Phlexible.inlineIcon('p-task-status_' + s + '-icon') + ' ' + Phlexible.tasks.Strings[s];
                }
            }, {
                header: this.strings.title,
                dataIndex: 'title',
                width: 140
            }, {
                header: this.strings.task,
                dataIndex: 'text',
                width: 150
            }, {
                header: this.strings.description,
                dataIndex: 'description',
                width: 150
            }, {
                header: this.strings.assigned_to,
                dataIndex: 'assigned_user',
                width: 100
            }, {
                header: this.strings.create_user,
                dataIndex: 'create_user',
                width: 100
            }, {
                header: this.strings.create_date,
                dataIndex: 'create_date',
                width: 120,
                hidden: true
            }];

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true,
            listeners: {
                selectionchange: function (sm) {
                    var r = sm.getSelected();
                    if (!r) {
                        return;
                    }
                    this.fireEvent('taskchange', r);
                },
                scope: this
            }
        });

        this.tbar = [{
            text: this.strings.reload,
            iconCls: 'p-task-reset-icon',
            handler: function () {
                this.store.reload();
            },
            scope: this
        }];

        this.bbar = new Ext.PagingToolbar({
            store: this.store
        });

        Phlexible.tasks.TasksGrid.superclass.initComponent.call(this);
    },

    setStatus: function (task_id, new_status, comment) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('tasks_create_status'),
            params: {
                task_id: task_id,
                new_status: new_status,
                comment: encodeURIComponent(comment)
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    Phlexible.success(data.msg);

                    this.store.reload();
                } else {
                    Ext.MessageBox.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    }
});

Ext.reg('tasks-tasksgrid', Phlexible.tasks.TasksGrid);