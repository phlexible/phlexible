Ext.ns('Phlexible.elements');

Phlexible.elements.TaskBar = Ext.extend(Ext.Toolbar, {
    strings: Phlexible.tasks.Strings,
    cls: 'p-tasks-toolbar',

    initComponent: function () {
        this.items = [/*{
         // items[0]
         iconCls: 'p-tasks-component-icon'
         },*/
            Phlexible.inlineIcon('p-tasks-component-icon'),
            // items[1]
            this.strings.task + ':',
            // items[2]
            '?',
            // items[3]
            '-',
            // items[4]
            this.strings.status + ':',
            // items[5]
            '?',
            // items[6]
            '-',
            // items[7]
            '?',
            {
                // items[8]
                text: this.strings.details,
                handler: function () {
                    if (!this.task.id) {
                        return;
                    }
                    var w = new Phlexible.tasks.ViewWindow();
                    w.show(this.task.id);
                },
                scope: this
            },
            // items[9]
            '-',
            // items[10]
            this.strings.action + ':',
            {
                // items[11]
                text: this.strings.finish,
                iconCls: 'p-tasks-status_finished-icon',
                handler: function () {
                    this.onSetStatus('finished');
                },
                scope: this
            }, ' ', {
                // items[13]
                text: this.strings.reject,
                iconCls: 'p-tasks-status_rejected-icon',
                handler: function () {
                    this.onSetStatus('rejected');
                },
                scope: this
            }, ' ', {
                // items[15]
                text: this.strings.reopen,
                iconCls: 'p-tasks-status_reopened-icon',
                handler: function () {
                    this.onSetStatus('reopened');
                },
                scope: this
            }, ' ', {
                // items[17]
                text: this.strings.close,
                iconCls: 'p-tasks-status_closed-icon',
                handler: function () {
                    this.onSetStatus('closed');
                },
                scope: this
            }];

        this.element.on({
            getlock: {
                fn: this.onGetLock,
                scope: this
            },
            islocked: {
                fn: this.onIsLocked,
                scope: this
            },
            removelock: {
                fn: this.onRemoveLock,
                scope: this
            }
        });

        Phlexible.elements.TaskBar.superclass.initComponent.call(this);
    },

    updateTaskInfo: function () {

        if (!this.element.data || !this.element.data.task) {
            this.hide();
        }
        else {
            this.task = task = this.element.data.task;

            switch (task.status) {
                case 'open':
                case 'reopened':
                    if (task.type === 2) {
                        this.items.items[9].show();
                        this.items.items[10].show();
                        this.items.items[11].setVisible(task.generic);
                        this.items.items[13].show();
                    }
                    else {
                        this.items.items[9].hide();
                        this.items.items[10].hide();
                        this.items.items[11].hide();
                        this.items.items[13].hide();
                    }
                    this.items.items[15].hide();
                    this.items.items[17].hide();
                    break;

                case 'rejected':
                case 'finished':
                    this.items.items[11].hide();
                    this.items.items[13].hide();
                    if (task.type === 1) {
                        this.items.items[9].show();
                        this.items.items[10].show();
                        this.items.items[15].show();
                        this.items.items[17].show();
                    }
                    else {
                        this.items.items[9].hide();
                        this.items.items[10].hide();
                        this.items.items[15].hide();
                        this.items.items[17].hide();
                    }
                    break;
            }

            var description = null;

            switch (task.type) {
                case 1:
                    description = String.format(this.strings.for_creator, task.date, task.time, task.recipient, task.text);
                    break;

                case 2:
                    description = String.format(this.strings.for_recipient, task.date, task.time, task.creator, task.text);
                    break;

                case 3:
                    description = String.format(this.strings.for_others, task.creator, task.date, task.time, task.recipient, task.text);
                    break;
            }

            this.items.items[2].setText(task.text);
            this.items.items[5].setText('<b>' + this.strings[task.status] + '</b>');
            this.items.items[7].setText(description);

            if (description) {
                this.show();
            }
            else {
                this.hide();
            }
        }

        this.ownerCt.doLayout();
    },

    onSetStatus: function (status) {
        if (!this.task) {
            return;
        }

        Ext.MessageBox.prompt(Phlexible.tasks.Strings.comment, String.format(Phlexible.tasks.Strings.status_text, Phlexible.tasks.Strings[status]), function (btn, text) {
            if (btn == 'ok') {
                this.setStatus(status, text);
            }
        }, this, true);
    },

    setStatus: function (new_status, comment) {
        if (!this.task) {
            return;
        }

        Phlexible.tasks.Manager.setStatus(this.task.id, new_status, comment, function (response) {
            var data = Ext.decode(response.responseText);

            if (data.success) {
                Phlexible.success(data.msg);

                this.fireEvent('newStatus', this);
            }
            else {
                Ext.MessageBox.alert('Failure', data.msg);
            }
        }, this);
    },

    onGetLock: function () {
        this.updateTaskInfo();
    },

    onIsLocked: function () {
        this.updateTaskInfo();
    },

    onRemoveLock: function () {
        this.updateTaskInfo();
    }
});

Ext.reg('elements-taskbar', Phlexible.elements.TaskBar);
