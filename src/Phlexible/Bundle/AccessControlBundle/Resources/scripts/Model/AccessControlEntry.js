Ext.provide('Phlexible.accesscontrol.model.AccessControlEntry');

Phlexible.accesscontrol.model.AccessControlEntry = Ext.data.Record.create([
    'type',
    'objectType',
    'objectId',
    'label',
    'language',
    'rights',
    'original',
    'above',
    'inherited',
    'setHere',
    'restore',
    'new'
]);