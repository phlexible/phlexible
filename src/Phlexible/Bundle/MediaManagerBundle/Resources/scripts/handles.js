Ext.require('Phlexible.Handles');
Ext.require('Phlexible.gui.menuhandle.handle.XtypeHandle');
Ext.require('Phlexible.mediamanager.MediamanagerPanel');

Phlexible.Handles.add('media', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.mediamanager.Strings.media,
        iconCls: 'p-mediamanager-component-icon',
        component: 'mediamanager-mainpanel'
    });
});
