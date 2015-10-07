Ext.provide('Phlexible.users.model.User');

Phlexible.users.model.User = Ext.data.Record.create([
    {name: 'uid', type: 'string'},
    {name: 'username', type: 'string'},
    {name: 'email', type: 'string'},
    {name: 'firstname', type: 'string'},
    {name: 'lastname', type: 'string'},
    {name: 'comment', type: 'string'},
    {name: 'roles'},
    {name: 'groups'},
    {name: 'enabled', type: 'boolean'},
    {name: 'expiresAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'credentialsExpireAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'expired', type: 'boolean'},
    {name: 'credentialsExpired', type: 'boolean'},
    {name: 'createDate', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'createUser'},
    {name: 'modifyDate', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'modifyUser'},
    {name: 'properties'}
]);
