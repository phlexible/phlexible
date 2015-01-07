Ext.provide('Phlexible.mediamanager.model.Rights');

/**
 * TODO: not used?
 */
Phlexible.mediamanager.model.Rights = Ext.data.Record.create([
    {name: 'id', type: 'string'},
    {name: 'name', type: 'string'},
    {name: 'type', type: 'string'},
    {name: 'folder_read', type: 'bool'},
    {name: 'folder_create', type: 'bool'},
    {name: 'folder_modify', type: 'bool'},
    {name: 'folder_delete', type: 'bool'},
    {name: 'folder_rights', type: 'bool'},
    {name: 'file_read', type: 'bool'},
    {name: 'file_create', type: 'bool'},
    {name: 'file_modify', type: 'bool'},
    {name: 'file_delete', type: 'bool'},
    {name: 'file_download', type: 'bool'},
    {name: 'inherited', type: 'int'},
    {name: 'stop', type: 'bool'},
    {name: 'original'},
    {name: 'restore', type: 'bool'}
]);

