Ext.namespace('Phlexible.siteroots.menuhandle');

Phlexible.siteroots.SiterootRecord = Ext.data.Record.create([
    {name: 'id'},
    {name: 'title'}
]);

Phlexible.siteroots.UrlRecord = Ext.data.Record.create([
    {name: 'id'},
    {name: 'global_default', type: 'boolean'},
    {name: 'default', type: 'boolean'},
    {name: 'hostname'},
    {name: 'language'},
    {name: 'target'}
]);

Phlexible.siteroots.ShortUrlRecord = Ext.data.Record.create([
    {name: 'id'},
    {name: 'hostname'},
    {name: 'path'},
    {name: 'language'},
    {name: 'target'}
]);

Phlexible.siteroots.NavigationRecord = Ext.data.Record.create([
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

Phlexible.siteroots.SpecialTidRecord = Ext.data.Record.create([
    {name: 'key'},
    {name: 'language'},
    {name: 'tid'}
]);

Phlexible.siteroots.ContentChannelRecord = Ext.data.Record.create([
    {name: 'contentchannel_id'},
    {name: 'contentchannel'},
    {name: 'used'},
    {name: 'default'}
]);

/**
 * Regular expression to check urls.
 */
Phlexible.siteroots.regexUrl = /^[a-z0-9\.\-]+\.[a-z0-9\.\-]+$/;

/**
 * Input mask for check urls.
 */
Phlexible.siteroots.maskUrl = /[a-z0-9\.\-]/;
