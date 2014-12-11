Ext.ux.Portlet = Ext.extend(Ext.Panel, {
    anchor: '100%',
    frame: true,
    collapsible: true,
    draggable: true,
    cls: 'x-portlet',

    initComponent: function () {
        if (this.extraCls) {
            this.cls += ' ' + this.extraCls;
        }
        Ext.ux.Portlet.superclass.initComponent.call(this);
    }
});
Ext.reg('portlet', Ext.ux.Portlet);
