Ext.ns('Phlexible.users.model');

Phlexible.users.model.User = Ext.data.Record.create([
    {name: 'uid'},
    {name: 'username'},
    {name: 'email'},
    {name: 'firstname'},
    {name: 'lastname'},
    {name: 'comment'},
    {name: 'roles'},
    {name: 'groups'},
    {name: 'expireDate', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'createDate', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'createUser'},
    {name: 'modifyDate', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'modifyUser'},
    {name: 'properties'}
]);
