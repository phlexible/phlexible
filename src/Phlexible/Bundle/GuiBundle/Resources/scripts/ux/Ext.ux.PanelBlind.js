Ext.provide('Ext.ux.PanelBlind');

Ext.ux.PanelBlind = Ext.extend(Ext.Panel, {

    constructor: function (config) {
        config = Ext.apply({
            autoHeight: true,
            floating: true,
            cls: 'x-blind',
            bodyStyle: {
                'border-width': '0px'
            },
            buttonAlign: 'center',
            buttons: [
                {
                    text: 'Dismiss',
                    handler: this.dismiss,
                    scope: this
                }
            ]
        }, config);
        Ext.ux.PanelBlind.superclass.constructor.call(this, config);
    },

    init: function (client) {
        this.client = client;
        client.blind = this;
        this.client.constructor.prototype.showBlind = this.clientShowBlind;
        this.client.constructor.prototype.dismissBlind = this.clientDismissBlind;
        this.client.on('destroy', this.destroy, this);
    },

    clientShowBlind: function () {
        this.blind.show();
    },

    clientDismissBlind: function () {
        this.blind.dismiss();
    },

    show: function () {
        var mask = this.client.getEl().mask();
        if (!this.rendered) {
            this.render(this.client.getEl());
        }

//      Synchronize this blind's z-index to be one above the client Element's mask
        this.el.setZIndex(Number(mask.getStyle('z-index')) + 1);

        this.el.disableShadow();
        this.setWidth(this.client.body.getSize(true).width);
        this.el.alignTo(this.client.body, 'tl-tl');
        this.el.slideIn('t', {
            callback: function () {
                this.el.visible = true; // Ext bug. Flag not set causes enableShadow to fail.
                this.el.enableShadow(true);
            },
            scope: this
        });
    },

    dismiss: function () {
        this.el.disableShadow();
        this.el.slideOut('t');
        this.client.getEl().unmask();
    }

});