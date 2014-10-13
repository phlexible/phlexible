Phlexible.tasks.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.tasks.Strings.tasks,
    strings: Phlexible.tasks.Strings,
    cls: 'p-tasks-main-panel',
    iconCls: 'p-task-component-icon',
    layout: 'border',
    border: false,

    params: {},

    loadParams: function (params) {
        if (params.id) {
            this.getComponent(1).getComponent(0).taskId = params.id;
            this.getComponent(0).onReset();
            this.getComponent(0).updateFilter();
        }
    },

    initComponent: function () {
        this.items = [
            {
                xtype: 'tasks-filterpanel',
                region: 'west',
                width: 200,
                collapsible: true,
                listeners: {
                    updateFilter: function (values) {
                        this.getComponent(1).getComponent(0).getStore().baseParams = values;
                        this.getComponent(1).getComponent(0).getStore().reload();
                    },
                    scope: this
                }
            },
            {
                xtype: 'panel',
                region: 'center',
                layout: 'border',
                border: false,
                items: [{
                    xtype: 'tasks-tasksgrid',
                    region: 'center',
                    taskId: this.params.id || false,
                    listeners: {
                        taskchange: function(r) {
                            var taskView = this.getTaskView(),
                                statusMenu = taskView.getTopToolbar().items.items[0].menu;
                            Phlexible.tasks.ViewTemplate.overwrite(taskView.body, r.data);
                            statusMenu.removeAll();
                            Ext.each(r.get('states'), function(state) {
                                statusMenu.add({
                                    text: state,
                                    iconCls: 'p-task-transition_' + state + '-icon'
                                });
                            });

                            Phlexible.tasks.CommentsTemplate.overwrite(this.getCommentsView().body, r.data.comments);
                            Phlexible.tasks.TransitionsTemplate.overwrite(this.getTransitionsView().body, r.data.transitions);
                        },
                        scope: this
                    }
                },{
                    region: 'east',
                    layout: 'border',
                    width: 400,
                    border: false,
                    items: [{
                        region: 'north',
                        height: 230,
                        html: '&nbsp;',
                        tbar: [{
                            text: '_status',
                            menu: []
                        },{
                            text: '_comment',
                            iconCls: 'p-task-comment-icon',
                            handler: function() {
                                var w = new Phlexible.tasks.CommentWindow();
                                w.show();
                            },
                            scope: this
                        },{
                            text: '_assign_to_me',
                            handler: function() {
                                var w = new Phlexible.tasks.AssignWindow();
                                w.show();
                            },
                            scope: this
                        },{
                            text: '_assign',
                            handler: function() {
                                var w = new Phlexible.tasks.AssignWindow();
                                w.show();
                            },
                            scope: this
                        }]
                    },{
                        xtype: 'tabpanel',
                        region: 'center',
                        activeTab: 0,
                        deferredRender: false,
                        items: [{
                            title: this.strings.comments,
                            iconCls: 'p-task-comment-icon',
                            html: '&nbsp;'
                        },{
                            title: this.strings.transitions,
                            iconCls: 'p-task-transition-icon',
                            html: '&nbsp;'
                        }]
                    }]
                }]
            }
        ];

        Phlexible.tasks.MainPanel.superclass.initComponent.call(this);
    },

    getFilterPanel: function() {
        return this.getComponent(0);
    },

    getTaskGrid: function() {
        return this.getComponent(1).getComponent(0);
    },

    getTaskView: function() {
        return this.getComponent(1).getComponent(1).getComponent(0);
    },

    getCommentsView: function() {
        return this.getComponent(1).getComponent(1).getComponent(1).getComponent(0);
    },

    getTransitionsView: function() {
        return this.getComponent(1).getComponent(1).getComponent(1).getComponent(1);
    }
});

Ext.reg('tasks-mainpanel', Phlexible.tasks.MainPanel)