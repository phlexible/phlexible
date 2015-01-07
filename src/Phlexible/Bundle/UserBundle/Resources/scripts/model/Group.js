Ext.provide('Phlexible.users.model.Group');

Phlexible.users.model.Group = Ext.data.Record.create([
    {name: 'gid'},
    {name: 'name'},
    {name: 'readonly'},
    {name: 'memberCnt'},
    {name: 'members'}
]);
