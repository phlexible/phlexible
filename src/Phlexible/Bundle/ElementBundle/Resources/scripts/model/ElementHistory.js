Ext.ns('Phlexible.elements.model');

Phlexible.elements.model.ElementHistory = Ext.data.Record.create([
    {name: 'type', type: 'string'},
    {name: 'id', type: 'string'},
    {name: 'tid', type: 'string'},
    {name: 'eid', type: 'string'},
    {name: 'version', type: 'string'},
    {name: 'language', type: 'string'},
    {name: 'comment', type: 'string'},
    {name: 'action', type: 'string'},
    {name: 'username', type: 'string'},
    {name: 'create_time', type: 'string'}
]);
