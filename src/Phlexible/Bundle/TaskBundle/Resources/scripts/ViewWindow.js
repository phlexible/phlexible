Phlexible.tasks.ViewWindow = Ext.extend(Ext.Window, {
    title: 'view',
    strings: Phlexible.tasks.Strings,
    cls: 'p-tasks-view-window',
    iconCls: 'p-task-component-icon',
    width: 700,
    minWidth: 700,
    height: 500,
    minHeight: 500,
    layout: 'border',
    constrainHeader: true,
    modal: true,

    task_id: false,
    data: {},

    initComponent: function() {
        this.items = [{
            border: false,
            region: 'north',
            cls: 'p-tasks-view-header',
            height: 100,
            html: 'test1',
            listeners: {
                render: function(c) {
					Phlexible.tasks.ViewTemplate.overwrite(c.el, this.data);
				},
				scope: this
            }
        },{
            border: false,
            region: 'center',
            cls: 'p-tasks-view-history',
            autoScroll: true,
            html: 'test2',
            listeners: {
                render: function(c) {
					Phlexible.tasks.HistoryTemplate.overwrite(c.el, this.data);
				},
				scope: this
            }
        }];

        this.buttons = [{
            text: this.strings.close,
            handler: this.close,
            scope: this
        }];

		Phlexible.tasks.ViewWindow.superclass.initComponent.call(this);
    },

    show: function(task_id) {
        if (!task_id) {
            return;
        }

        this.task_id = task_id;

        Ext.Ajax.request({
            url: Phlexible.Router.generate('tasks_view'),
            params: {
                task_id: task_id
            },
            success: function(response) {
                var data = Ext.decode(response.responseText);

                this.data = data;

                this.setTitle(data.text);

                Phlexible.tasks.ViewWindow.superclass.show.call(this);
            },
            scope: this
        });
    }
});
