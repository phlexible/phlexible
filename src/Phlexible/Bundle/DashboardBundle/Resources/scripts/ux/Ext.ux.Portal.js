Ext.ux.Portal = Ext.extend(Ext.Panel, {
    layout: 'column',
    autoScroll: true,
    cls: 'x-portal',
    defaultType: 'portalcolumn',

    initComponent: function () {
        Ext.ux.Portal.superclass.initComponent.call(this);
        this.addEvents({
            validatedrop: true,
            beforedragover: true,
            dragover: true,
            beforedrop: true,
            drop: true
        });
    },

    initEvents: function () {
        Ext.ux.Portal.superclass.initEvents.call(this);
        this.dd = new Ext.ux.Portal.DropZone(this, this.dropConfig);
    },

    beforeDestroy: function () {
        if (this.dd) {
            this.dd.unreg();
        }
        Ext.ux.Portal.superclass.beforeDestroy.call(this);
    }
});
Ext.reg('portal', Ext.ux.Portal);
