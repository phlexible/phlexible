Phlexible.elements.menuhandle.ElementHandle = Ext.extend(Phlexible.gui.menuhandle.handle.XtypeHandle, {
    iconCls: 'p-element-component-icon',
    component: 'elements-main',

    getText: function() {
        return this.parameters.title;
    }
});
