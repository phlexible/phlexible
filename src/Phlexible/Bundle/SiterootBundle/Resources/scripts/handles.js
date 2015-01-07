Ext.require('Phlexible.Handles');
Ext.require('Phlexible.gui.menuhandle.handle.XtypeHandle');
Ext.require('Phlexible.siteroots.MainPanel');

Phlexible.Handles.add('siteroots', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.siteroots.Strings.siteroots,
        iconCls: 'p-siteroot-component-icon',
        component: 'siteroots-main'
    });
});
