Ext.namespace('Phlexible.queue.model');

Phlexible.queue.model.Job = Ext.data.Record.create([
    'id',
    'command',
    'priority',
    'status',
    'create_time',
    'start_time',
    'end_time',
    'output'
]);