Ext.namespace('Phlexible.tasks.model');

Phlexible.tasks.model.Task = Ext.data.Record.create([
    {name: 'id'},
    {name: 'type'},
    {name: 'generic'},
    {name: 'title'},
    {name: 'text'},
    {name: 'description'},
    {name: 'component'},
    {name: 'created'},
    {name: 'link'},
    {name: 'assigned_user'},
    {name: 'status'},
    {name: 'create_user'},
    {name: 'create_date'},
    {name: 'transitions'},
    {name: 'comments'},
    {name: 'states'}
]);
