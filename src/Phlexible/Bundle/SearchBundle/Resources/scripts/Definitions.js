Ext.namespace('Phlexible.search');

Phlexible.search.SearchRecord = Ext.data.Record.create([
    'id',
    'image',
    'title',
    'author',
    {name: 'date', type: 'date', dateFormat: 'timestamp'},
    'component',
    'menu'
]);
