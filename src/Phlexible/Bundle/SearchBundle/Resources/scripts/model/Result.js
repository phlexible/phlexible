Ext.namespace('Phlexible.search.model');

Phlexible.search.model.Result = Ext.data.Record.create([
    'id',
    'image',
    'title',
    'author',
    {name: 'date', type: 'date', dateFormat: 'timestamp'},
    'component',
    'menu'
]);
