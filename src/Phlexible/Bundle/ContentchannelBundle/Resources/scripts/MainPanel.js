Ext.ns('Phlexible.contentchannels');

Phlexible.contentchannels.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.contentchannels.Strings.contentchannels,
    strings: Phlexible.contentchannels.Strings,
    layout: 'border',
    iconCls: 'p-contentchannel-component-icon',

    initComponent: function () {
        this.items = [
            {
                xtype: 'contentchannels-contentchannelsgrid',
                region: 'west',
                width: 200,
                listeners: {
                    contentchannel_select: {
                        fn: function (r) {
                            this.getComponent(1).loadData(r);
                        },
                        scope: this
                    }
                }
            },
            {
                xtype: 'contentchannels-contentchannelsform',
                region: 'center',
                listeners: {
                    contentchannel_save: {
                        fn: function () {
                            this.getComponent(0).getStore().reload();
                        },
                        scope: this
                    }
                }
            }
        ];

        Phlexible.contentchannels.MainPanel.superclass.initComponent.call(this);
    },

    loadParams: function (params) {

    }
});

Ext.reg('contentchannels-main', Phlexible.contentchannels.MainPanel);