Ext.require('Phlexible.Handles');
Ext.require('Phlexible.gui.menuhandle.handle.XtypeHandle');
Ext.require('Phlexible.contentchannels.MainPanel');

Phlexible.Handles.add('contentchannels', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.contentchannels.Strings.contentchannels,
        iconCls: 'p-contentchannel-component-icon',
        component: 'contentchannels-main'
    });
});
