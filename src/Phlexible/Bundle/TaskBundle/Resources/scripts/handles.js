Ext.require('Phlexible.Handles');
Ext.require('Phlexible.gui.menuhandle.handle.XtypeHandle');
Ext.require('Phlexible.tasks.MainPanel');

Phlexible.Handles.add('tasks', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.tasks.Strings.tasks,
        iconCls: 'p-task-component-icon',
        component: 'tasks-mainpanel'
    });
});
