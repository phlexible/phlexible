Ext.provide('Phlexible.accesscontrol.model.AccessControlEntry');

Phlexible.accesscontrol.model.AccessControlEntry = Ext.data.Record.create([
    {name: 'id'},
    {name: 'objectType'},
    {name: 'objectId'},
    {name: 'mask'},
    {name: 'stopMask'},
    {name: 'noInheritMask'},
    {name: 'parentMask'},
    {name: 'parentStopMask'},
    {name: 'parentNoInheritMask'},
    {name: 'objectLanguage'},
    {name: 'securityType'},
    {name: 'securityId'},
    {name: 'securityName'}
]);
