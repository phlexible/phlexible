Ext.namespace('Phlexible.tasks.model');

Phlexible.tasks.model.Comment = Ext.data.Record.create([
    {name: 'id'},
    {name: 'current_state'},
    {name: 'comment'},
    {name: 'create_user'},
    {name: 'create_date'}
]);
