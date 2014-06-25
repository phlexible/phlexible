Phlexible.tasks.TasksGrid = Ext.extend(Ext.grid.GridPanel, {
    strings: Phlexible.tasks.Strings,
    cls: 'p-tasks-task-grid',
    viewConfig: {
        deferEmptyText: false,
        emptyText: Phlexible.tasks.Strings.no_tasks_found
    },
	autoExpandColumn: 6,
	loadMask: true,

    initComponent: function() {
        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('tasks_list'),
            baseParams: {
                start: 0,
                limit: 20
            },
            fields: ['id', 'type', 'generic', 'title', 'text', 'component', 'created', 'received', 'link', 'assigned_user', 'latest_status', 'latest_comment', 'latest_user', 'latest_date', 'create_user', 'create_date', 'latest_id', 'history'],
            root: 'tasks',
            id: 'id',
            totalProperty: 'total',
            remoteSort: true,
            listeners: {
                load: function() {
					if (this.taskId) {
						var row = this.getStore().find('id', this.taskId);
						if (row !== -1) {
							this.getSelectionModel().selectRow(row);
							this.expander.toggleRow(row);
						}
						this.taskId = false;
					}
				},
				scope: this
            }
        });

        var expander = this.expander = new Ext.grid.RowExpander({
            dataIndex: 'history',
            tpl: Phlexible.tasks.HistoryTemplate,
            enableCaching: false,
            lazyRender: false
        });

        this.columns = [
            expander,
        {
            header: this.strings.id,
            dataIndex: 'id',
            width: 220,
            hidden: true
        },{
            header: this.strings.type,
            dataIndex: 'type',
            width: 200,
            hidden: true
        },{
            header: this.strings.component,
            dataIndex: 'component',
            width: 100,
            hidden: true
        },{
            header: this.strings.status,
            dataIndex: 'latest_status',
            width: 150,
            renderer: function(s) {
                return Phlexible.inlineIcon('p-task-status_'+s+'-icon') + ' ' + Phlexible.tasks.Strings[s];
            }
        },{
            header: this.strings.title,
            dataIndex: 'title',
            width: 140
        },{
            header: this.strings.task,
            dataIndex: 'text',
            width: 150
        },{
            header: this.strings.comment,
            dataIndex: 'latest_comment',
            width: 150,
            renderer: function(s) {
                if (!s) return '-';
                return s;
            }
        },{
            header: this.strings.assigned_to,
            dataIndex: 'assigned_user',
            width: 100
        },{
            header: this.strings.change_user,
            dataIndex: 'latest_user',
            width: 100
        },{
            header: this.strings.change_date,
            dataIndex: 'latest_date',
            width: 120
        },{
            header: this.strings.create_user,
            dataIndex: 'create_user',
            width: 100,
            hidden: true
        },{
            header: this.strings.create_date,
            dataIndex: 'create_date',
            width: 120,
            hidden: true
        }];

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true,
            listeners: {
                selectionchange: function(sm) {
					var r = sm.getSelected();
					var tb = this.getTopToolbar();
					if(r) {
						if (r.get('link')) {
							tb.items.items[5].enable();
						}
						else {
							tb.items.items[5].disable();
						}

						var status   = r.data.latest_status;
						var created  = r.data.created;
						var received = r.data.received;

						var sep = false;
						if(received && (status == 'open' || status == 'reopened')) {
							tb.items.items[0].show();
							tb.items.items[1].hide();
							tb.items.items[2].setVisible(r.data.generic);
							sep = true;
						}
						else if(created && status == 'rejected') {
							tb.items.items[0].hide();
							tb.items.items[1].show();
							tb.items.items[2].hide();
							sep = true;
						}
						else if(created && status == 'finished') {
							tb.items.items[0].hide();
							tb.items.items[1].show();
							tb.items.items[2].hide();
							sep = true;
						}
						else {
							tb.items.items[0].hide();
							tb.items.items[1].hide();
							tb.items.items[2].hide();
							sep = false;
						}

						if (created && status != 'closed') {
							tb.items.items[3].show();
							sep = true;
						}
						else {
							tb.items.items[3].hide();
						}

						if(sep) {
							tb.items.items[4].show();
						} else {
							tb.items.items[4].hide();
						}
					} else {
						tb.items.items[0].hide();
						tb.items.items[1].hide();
						tb.items.items[2].hide();
						tb.items.items[3].hide();
						tb.items.items[4].hide();
						tb.items.items[5].disable();
					}
				},
				scope: this
            }
        });

        this.tbar = [{
            // items[0]
            text: this.strings.reject,
            iconCls: 'p-task-status_rejected-icon',
            hidden: true,
            handler: function() {
                this.onSetStatus('rejected');
            },
            scope: this
        },{
            // items[1]
            text: this.strings.reopen,
            iconCls: 'p-task-status_reopened-icon',
            hidden: true,
            handler: function() {
                this.onSetStatus('reopened');
            },
            scope: this
        },{
            // items[2]
            text: this.strings.finish,
            iconCls: 'p-task-status_finished-icon',
            hidden: true,
            handler: function() {
                this.onSetStatus('finished');
            },
            scope: this
        },{
            // items[3]
            text: this.strings.close,
            iconCls: 'p-task-status_closed-icon',
            hidden: true,
            handler: function() {
                this.onSetStatus('closed');
            },
            scope: this
        },'-',{
            // items[5]
            text: this.strings.open_target,
            iconCls: 'p-task-goto-icon',
            disabled: true,
            handler: function() {
                var r = this.getSelectionModel().getSelected();

                if(!r) {
                    return;
                }

                var menu = r.get('link');

                if (menu && menu.handler) {
                    var handler = menu.handler;
                    if (typeof handler == 'string') {
                       handler = Phlexible.evalClassString(handler);
                    }
                    handler(menu);
                }
            },
            scope: this
        },'-',{
            // items[7]
            text: this.strings.reload,
            iconCls: 'p-task-reset-icon',
            handler: function() {
                this.store.reload();
            },
            scope: this
        }];

        this.bbar = new Ext.PagingToolbar({
            store: this.store
        });

        this.plugins = [expander];

        Phlexible.tasks.TasksGrid.superclass.initComponent.call(this);
    },

    onSetStatus: function(status) {
        var r = this.getSelectionModel().getSelected();

        if(!r) {
            return;
        }

        Ext.MessageBox.prompt(this.strings.comment, String.format(this.strings.status_text, this.strings[status]), function(btn, text) {
            if (btn == 'ok') {
                this.setStatus(r.data.id, status, text);
            }
        }, this, true);
    },

    setStatus: function(task_id, new_status, comment) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('tasks_create_status'),
            params: {
                task_id: task_id,
                new_status: new_status,
                comment: encodeURIComponent(comment)
            },
            success: function(response) {
                var data = Ext.decode(response.responseText);

                if(data.success) {
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