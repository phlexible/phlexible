Ext.namespace('Phlexible.gui');

Phlexible.gui.Help = Ext.extend(Ext.ux.ManagedIframePanel, {
    title: Phlexible.gui.Strings.help,
    iconCls: 'p-gui-help-icon',
    xtype: 'iframepanel',
    closable: false,

    initComponent: function() {
        this.defaultSrc = 'http://www.test.de';

        this.tbar = [{
            text: Phlexible.gui.Strings.home,
            iconCls: 'p-gui-home-icon',
            handler: function() {
                this.setSrc(this.defaultSrc);
            },
            scope: this
        }];

        Phlexible.gui.Help.superclass.initComponent.call(this);
    }
});

Ext.reg('gui-help', Phlexible.gui.Help);
