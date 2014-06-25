Phlexible.elements.menuhandle.ElementHandle = Ext.extend(Phlexible.gui.menuhandle.handle.XtypeHandle, {
    text: 'www.brainbits.net',
    iconCls: 'p-element-component-icon',
    component: 'elements-main',

    xgetParameters: function() {
		console.log(this);
        return {title: 'www.brainbits.net', siteroot_id: '48ef589f-2ddc-4cb6-9a54-7e0dc0a8005b'};
    }
});
