Ext.provide('Phlexible.siteroots.model.Navigation');

Phlexible.siteroots.model.Navigation = Ext.data.Record.create([
    {name: 'id'},
    {name: 'title'},
    {name: 'handler'},
    {name: 'start_tid'},
    {name: 'max_depth'},
    {name: 'flags'},
    {name: 'supports'},
    {name: 'additional'},
    {name: 'hide_config', type: 'bool'}
]);
