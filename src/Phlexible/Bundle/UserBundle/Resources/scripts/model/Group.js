Ext.ns('Phlexible.users.model');

Phlexible.users.model.Group = Ext.data.Record.create([
    {name: 'gid'},
    {name: 'name'},
    {name: 'readonly'},
    {name: 'memberCnt'},
    {name: 'members'}
]);
