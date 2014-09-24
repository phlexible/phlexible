Ext.namespace('Phlexible.search.menuhandle');

Phlexible.search.SearchRecord = Ext.data.Record.create([
    'id',
    'image',
    'title',
    'author',
    {name: 'date', type: 'date', dateFormat: 'timestamp'},
    'component',
    'menu'
]);
