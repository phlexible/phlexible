Ext.provide('Phlexible.metasets.model.Metaset');

Phlexible.metasets.model.Metaset = Ext.data.Record.create([
    {name: 'id', type: 'string'},
    {name: 'name', type: 'string'},
    {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'createUser', type: 'string'},
    {name: 'modifiedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'modifyUser', type: 'string'},
    {name: 'revision', type: 'int'}
]);
