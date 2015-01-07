Ext.provide('Phlexible.tasks.ViewTemplate');
Ext.provide('Phlexible.tasks.CommentsTemplate');
Ext.provide('Phlexible.tasks.TransitionsTemplate');
Ext.provide('Phlexible.tasks.MainPanel');

Ext.require('Phlexible.tasks.FilterPanel');
Ext.require('Phlexible.tasks.TasksGrid');

Phlexible.tasks.ViewTemplate = new Ext.XTemplate(
    '<div class="p-tasks-view">',
    '<table cellpadding="0" cellspacing="5">',
    '<colgroup>',
    '<col width="100" />',
    '<col width="240" />',
    '</colgroup>',
    '<tr>',
    '<th>{[Phlexible.tasks.Strings.task]}</th>',
    '<td>{title}</td>',
    '</tr>',
    '<tr>',
    '<th>{[Phlexible.tasks.Strings.task]}</th>',
    '<td>{text}</td>',
    '</tr>',
    '<tr>',
    '<th>{[Phlexible.tasks.Strings.status]}</th>',
    '<td>{[Phlexible.inlineIcon(\"p-task-status_\"+values.status+\"-icon\")]} {[Phlexible.tasks.Strings.get(values.status)]}</td>',
    '</tr>',
    '<tr>',
    '<th colspan="2">{[Phlexible.tasks.Strings.description]}</th>',
    '</tr>',
    '<tr>',
    '<td colspan="2">{description}</td>',
    '</tr>',
    '<tr>',
    '<th>{[Phlexible.tasks.Strings.assigned_to]}</th>',
    '<td>{assigned_user}</td>',
    '</tr>',
    '<tr>',
    '<th>{[Phlexible.tasks.Strings.create_user]}</th>',
    '<td>{create_user}</td>',
    '</tr>',
    '<tr>',
    '<th>{[Phlexible.tasks.Strings.create_date]}</th>',
    '<td>{create_date}</td>',
    '</tr>',
    '</table>',
    '</div>'
);

Phlexible.tasks.CommentsTemplate = new Ext.XTemplate(
    '<div class="p-tasks-comments">',
    '<tpl for=".">',
    '<div class="p-tasks-comment">',
    '<div class="p-tasks-by">{create_user} kommentierte - {create_date}</div>',
    '<div class="p-tasks-text">{comment}</div>',
    '</div>',
    '</tpl>',
    '</div>'
);

Phlexible.tasks.TransitionsTemplate = new Ext.XTemplate(
    '<div class="p-tasks-transitions">',
    '<tpl for=".">',
    '<div class="p-tasks-transition">',
    '<div class="p-tasks-by">{create_user} änderte - {create_date}</div>',
    '<div class="p-tasks-text">' +
    '<div style="float: left;">{[Phlexible.inlineIcon(\"p-task-status_\" + values.old_state + \"-icon\")]} {old_state}</div>' +
    '<div style="margin-left: 120px;">{[Phlexible.inlineIcon(\"p-task-goto-icon\")]} ' +
    '{[Phlexible.inlineIcon(\"p-task-status_\" + values.new_state + \"-icon\")]} {new_state}</div>' +
    '<div style="clear: left; "></div>' +
    '</div>',
    '</div>',
    '</tpl>',
    '</div>'
);

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