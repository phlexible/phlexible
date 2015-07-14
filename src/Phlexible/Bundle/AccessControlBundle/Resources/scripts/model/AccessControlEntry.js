Ext.provide('Phlexible.accesscontrol.model.AccessControlEntry');

Phlexible.accesscontrol.model.AccessControlEntry = Ext.data.Record.create([
    {name: 'id'},
    {name: 'objectType'},
    {name: 'objectId'},
    {name: 'mask'},
    {name: 'inheritedMask'},
    {name: 'stopMask'},
    {name: 'noInheritMask'},
    {name: 'objectLanguage'},
    {name: 'securityType'},
    {name: 'securityId'},
    {name: 'securityName'}
]);
