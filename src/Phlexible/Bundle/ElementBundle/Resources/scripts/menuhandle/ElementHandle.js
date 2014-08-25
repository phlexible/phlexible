Phlexible.elements.menuhandle.ElementHandle = Ext.extend(Phlexible.gui.menuhandle.handle.XtypeHandle, {
    iconCls: 'p-element-component-icon',
    component: 'elements-main',

    getIdentifier: function () {
        return this.getComponent() + '_' + this.parameters.siteroot_id;
    },

    getText: function() {
        return this.parameters.title;
    }
});
