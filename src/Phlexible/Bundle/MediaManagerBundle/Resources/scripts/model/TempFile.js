Ext.provide('Phlexible.mediamanager.model.TempFile');

Phlexible.mediamanager.model.TempFile = Ext.data.Record.create([
    {name: 'versions', type: 'boolean'},
    {name: 'temp_key', type: 'string'},
    {name: 'temp_id', type: 'string'},
    {name: 'new_id', type: 'string'},
    {name: 'new_name', type: 'string'},
    {name: 'new_type', type: 'string'},
    {name: 'new_size', type: 'int'},
    {name: 'wizard', type: 'bool'},
    {name: 'total', type: 'int'},
    {name: 'old_name', type: 'string'},
    {name: 'old_id', type: 'string'},
    {name: 'old_type', type: 'string'},
    {name: 'old_size', type: 'int'},
    {name: 'alternative_name', type: 'string'},
    {name: 'files'}
]);
