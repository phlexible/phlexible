Ext.require('Phlexible.Handles');
Ext.require('Phlexible.gui.menuhandle.handle.WindowHandle');
Ext.require('Phlexible.queue.QueueStatsWindow');

Phlexible.Handles.add('queue', function() {
    return new Phlexible.gui.menuhandle.handle.WindowHandle({
        text: Phlexible.queue.Strings.jobs,
        iconCls: 'p-queue-stats-icon',
        component: 'Phlexible.queue.QueueStatsWindow'
    });
});
