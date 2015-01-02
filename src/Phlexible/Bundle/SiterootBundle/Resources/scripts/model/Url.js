Ext.namespace('Phlexible.siteroots.model');

Phlexible.siteroots.model.Url = Ext.data.Record.create([
    {name: 'id'},
    {name: 'global_default', type: 'boolean'},
    {name: 'default', type: 'boolean'},
    {name: 'hostname'},
    {name: 'language'},
    {name: 'target'}
]);
