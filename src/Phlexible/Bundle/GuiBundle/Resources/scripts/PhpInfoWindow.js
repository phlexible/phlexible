Phlexible.gui.PhpInfoWindow = Ext.extend(Ext.Window, {
    title: 'PHP Info',
    iconCls: 'p-gui-php-icon',
    width: 840,
    height: 600,
    constrain: true,
    maximizable: true,
    layout: 'fit',

    initComponent: function() {
        this.items = new Ext.ux.ManagedIframePanel({
            defaultSrc: Phlexible.Router.generate('gui_status_php'),
            disableMessaging: true
        });

        Phlexible.gui.PhpInfoWindow.superclass.initComponent.call(this);
    }
});
