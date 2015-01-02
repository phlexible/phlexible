Phlexible.Handles.add('media', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.mediamanager.Strings.media,
        iconCls: 'p-mediamanager-component-icon',
        component: 'mediamanager-mainpanel'
    });
});
