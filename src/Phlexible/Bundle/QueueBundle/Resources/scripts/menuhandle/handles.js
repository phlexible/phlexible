Phlexible.Handles.add('queue', function() {
    return new Phlexible.gui.menuhandle.handle.WindowHandle({
        text: Phlexible.queue.Strings.queue,
        iconCls: 'p-queue-stats-icon',
        component: 'Phlexible.queue.QueueStatsWindow'
    });
});
