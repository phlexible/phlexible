Ext.provide('Phlexible.tasks.FilterPanel');

Phlexible.tasks.FilterPanel = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.tasks.Strings.filter,
    strings: Phlexible.tasks.Strings,
    bodyStyle: 'padding: 5px;',
    cls: 'p-tasks-filter-panel',
    iconCls: 'p-task-filter-icon',
    autoScroll: true,

    initComponent: function () {
        this.task = new Ext.util.DelayedTask(this.updateFilter, this);

        this.items = [
            {
                xtype: 'panel',
                title: this.strings.comments,
                layout: 'form',
                frame: true,
                collapsible: true,
                labelAlign: 'top',
                hidden: true,
                items: [
                    {
                        xtype: 'textfield',
                        hideLabel: true,
                        anchor: '-25',
                        name: 'comments',
                        labelAlign: 'top',
                        enableKeyEvents: true,
                        listeners: {
                            keyup: function (field, event) {
                                if (event.getKey() == event.ENTER) {
                                    this.task.cancel();
                                    this.updateFilter();
                                    return;
                                }

                                this.task.delay(500);
                            },
                            scope: this
                        }
                    }
                ]
            },
            {
                xtype: 'panel',
                title: this.strings.tasks,
                layout: 'form',
                frame: true,
                collapsible: true,
                defaults: {
                    hideLabel: true
                },
                items: [
                    {
                        xtype: 'radio',
                        name: 'tasks',
                        boxLabel: this.strings.assigned_to_me,
                        inputValue: 'todos',
                        listeners: {
                            check: function (cb, checked) {
                                if (checked) {
                                    this.updateFilter();
                                }
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'radio',
                        name: 'tasks',
                        boxLabel: this.strings.created_by_me,
                        inputValue: 'tasks',
                        listeners: {
                            check: function (cb, checked) {
                                if (checked) {
                                    this.updateFilter();
                                }
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'radio',
                        name: 'tasks',
                        boxLabel: this.strings.involved,
                        inputValue: 'involved',
                        checked: true,
                        listeners: {
                            check: function (cb, checked) {
                                if (checked) {
                                    this.updateFilter();
                                }
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'radio',
                        name: 'tasks',
                        boxLabel: this.strings.all_tasks,
                        inputValue: 'all',
                        listeners: {
                            check: function (cb, checked) {
                                if (checked) {
                                    this.updateFilter();
                                }
                            },
                            scope: this
                        }
                    }
                ]
            },
            {
                xtype: 'panel',
                title: this.strings.status,
                layout: 'form',
                frame: true,
                collapsible: true,
                defaults: {
                    hideLabel: true
                },
                items: [
                    {
                        xtype: 'checkbox',
                        name: 'status_open',
                        boxLabel: Phlexible.inlineIcon('p-task-status_open-icon') + ' ' + this.strings.open,
                        checked: true,
                        listeners: {
                            check: this.updateFilter,
                            scope: this
                        }
                    },
                    {
                        xtype: 'checkbox',
                        name: 'status_rejected',
                        boxLabel: Phlexible.inlineIcon('p-task-status_rejected-icon') + ' ' + this.strings.rejected,
                        checked: true,
                        listeners: {
                            check: this.updateFilter,
                            scope: this
                        }
                    },
                    {
                        xtype: 'checkbox',
                        name: 'status_reopened',
                        boxLabel: Phlexible.inlineIcon('p-task-status_reopened-icon') + ' ' + this.strings.reopened,
                        checked: true,
                        listeners: {
                            check: this.updateFilter,
                            scope: this
                        }
                    },
                    {
                        xtype: 'checkbox',
                        name: 'status_finished',
                        boxLabel: Phlexible.inlineIcon('p-task-status_finished-icon') + ' ' + this.strings.finished,
                        checked: true,
                        listeners: {
                            check: this.updateFilter,
                            scope: this
                        }
                    },
                    {
                        xtype: 'checkbox',
                        name: 'status_closed',
                        boxLabel: Phlexible.inlineIcon('p-task-status_closed-icon') + ' ' + this.strings.closed,
                        listeners: {
                            check: this.updateFilter,
                            scope: this
                        }
                    }
                ]
            }
        ];

        /*
         this.tbar = ['->',{
         text: this.strings.reset,
         iconCls: 'p-task-reset-icon',
         handler: this.onReset,
         scope: this
         }];
         */

        this.on('render', function () {
            var values = {
                tasks: 'involved',
                status_open: 1,
                status_rejected: 1,
                status_reopened: 1,
                status_finished: 1
            };

            this.fireEvent('updateFilter', values);
        }, this);

//        Ext.Ajax.request({
//            url: Phlexible.Router.generate('tasks_filtervalues'),
//            success: this.onLoadFilterValues,
//            scope: this
//        });

        Phlexible.tasks.FilterPanel.superclass.initComponent.call(this);
    },

    onReset: function () {
        this.form.reset();
    },

    onLoadFilterValues: function (response) {
        var data = Ext.decode(response.responseText);

//        if(data.priorities && data.priorities.length && Ext.isArray(data.priorities)) {
//            Ext.each(data.priorities, function(item) {
//                this.getComponent(1).add({
//                    xtype: 'checkbox',
//                    name: 'priority_' + item.id,
//                    boxLabel: '<img src="' + Phlexible.bundleAsset(/resources/asset/icon/messages/priority_' + item.title + '.png)" style="vertical-align: middle;" width="16" height="16" /> ' + item.title,
//                    listeners: {
//                        check: this.updateFilter,
//                        scope: this
//                    }
//                });
//            }, this);
//            this.getComponent(1).items.each(function(item) {
//                this.form.add(item);
//            }, this);
//        }

        this.doLayout();
    },

    updateFilter: function () {
        var values = this.form.getValues();

        this.fireEvent('updateFilter', values);
    }
});

Ext.reg('tasks-filterpanel', Phlexible.tasks.FilterPanel);