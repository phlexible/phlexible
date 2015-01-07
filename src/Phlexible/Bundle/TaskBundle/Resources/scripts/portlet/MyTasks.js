Ext.provide('Phlexible.tasks.portlet.TaskRecord');
Ext.provide('Phlexible.tasks.portlet.MyTasksTemplate');
Ext.provide('Phlexible.tasks.portlet.MyTasks');

Phlexible.tasks.portlet.TaskRecord = Ext.data.Record.create([
    {name: 'id', type: 'string'},
    {name: 'type', type: 'string'},
    {name: 'text', type: 'string'},
    {name: 'status', type: 'string'},
    {name: 'create_uid', type: 'string'},
    {name: 'create_user', type: 'string'},
    {name: 'create_date', type: 'string'}
]);

Phlexible.tasks.portlet.MyTasksTemplate = new Ext.XTemplate(
    '<tpl for=".">',
    '<div id="portal_tasks_{id}" class="portlet-task" style="cursor: pointer; padding-bottom: 5px;">',
    '<div>',
    '<b><img src="{[Phlexible.bundleAsset("/tasks/icons/status_"+values.status+".png")]} width="16" height="16" style="vertical-align: middle;"> {[Phlexible.tasks.Strings.get(values.status)]}</b>, von<b> {create_user}</b>, <b>{create_date}</b>',
    '</div>',
    '<div style="padding-left: 20px;">',
    '{text}',
    '</div>',
    '<tpl if="comment">',
    '<div style="padding-left: 20px;">',
    '{[Phlexible.tasks.Strings.comment]}: {comment}',
    '</div>',
    '</tpl>',
    '</div>',
    '</tpl>'
);

Phlexible.tasks.portlet.MyTasks = Ext.extend(Ext.ux.Portlet, {
    bodyStyle: 'padding: 5px 5px 5px 5px',
    iconCls: 'p-task-portlet-icon',
    strings: Phlexible.tasks.Strings,
    title: Phlexible.tasks.Strings.my_tasks,

    initComponent: function () {

        this.store = new Ext.data.SimpleStore({
            fields: Phlexible.tasks.portlet.TaskRecord,
            id: 'id'
            //sortInfo: {field: 'type', username: 'ASC'},
        });

        var data = this.record.get('data');
        if (data) {
            Ext.each(data, function (item) {
                this.add(new Phlexible.tasks.portlet.TaskRecord(item, item.id));
            }, this.store);
        }

        this.items = [
            {
                xtype: 'dataview',
                itemSelector: 'div.portlet-task',
                style: 'overflow: auto',
                singleSelect: true,
                emptyText: this.strings.no_active_tasks,
                deferEmptyText: false,
                autoHeight: true,
                store: this.store,
                tpl: Phlexible.tasks.portlet.MyTasksTemplate,
                listeners: {
                    dblclick: function (view, index) {
                        r = view.store.getAt(index);

                        if (!r) {
                            return;
                        }

                        Phlexible.Frame.loadPanel(
                            'Phlexible_tasks_MainPanel',
                            Phlexible.tasks.MainPanel,
                            {
                                id: r.get('id')
                            }
                        );
                    },
                    scope: this
                }
            }
        ];

        Phlexible.tasks.portlet.MyTasks.superclass.initComponent.call(this);
    },

    updateData: function (data) {
        var taskMap = [];

        for (var i = 0; i < data.length; i++) {
            var row = data[i];
            taskMap.push(row.id);
            var r = this.store.getById(row.id);
            var update = false;
            if (r) {
                if (r.get('status') != row.status) {
                    update = true;
                    r.set('status', row.status);
                }
                if (r.get('text') != row.text) {
                    update = true;
                    r.set('text', row.text);
                }
            } else {
                update = true;
                this.store.add(new Phlexible.tasks.portlet.TaskRecord(row, row.id));

//                Phlexible.msg('Task', this.strings.new_task + ' "' + row.text + '".');
            }

            if (update) {
                Ext.fly('portal_tasks_' + row.id).frame('#8db2e3', 1);
            }
        }

        for (var i = this.store.getCount() - 1; i > 0; i--) {
            var r = this.store.getAt(i);
            if (taskMap.indexOf(r.id) == -1) {
//                Phlexible.msg('Task', this.strings.new_task + ' "' + r.get('text') + '".');
                this.store.remove(r);
            }
        }

        if (!this.store.getCount()) {
            this.store.removeAll();
        }

        this.store.sort('type', 'ASC');
    }
});

Ext.reg('tasks-portlet-mytasks', Phlexible.tasks.portlet.MyTasks);