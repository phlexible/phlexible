Phlexible.Handles.add('mediatypes', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.mediatype.Strings.media_types,
        iconCls: 'p-mediatype-component-icon',
        component: 'mediatype-mainpanel'
    });
});
