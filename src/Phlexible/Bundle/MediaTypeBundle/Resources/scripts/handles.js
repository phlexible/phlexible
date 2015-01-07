Ext.require('Phlexible.Handles');
Ext.require('Phlexible.gui.menuhandle.handle.XtypeHandle');
Ext.require('Phlexible.mediatype.MainPanel');

Phlexible.Handles.add('mediatypes', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.mediatype.Strings.media_types,
        iconCls: 'p-mediatype-component-icon',
        component: 'mediatype-mainpanel'
    });
});
