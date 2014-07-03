Phlexible.gui.BundlesMainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.gui.Strings.bundles,
    strings: Phlexible.gui.Strings,
    iconCls: 'p-gui-manager-icon',
    closable: true,
    layout: 'border',

    initComponent: function () {
        this.items = [
            {
                xtype: 'gui-bundles-filter',
                region: 'west',
                width: 200,
                listeners: {
                    updateFilter: {
                        fn: function (data) {
                            this.getComponent(1).setFilterData(data);
                        },
                        scope: this
                    }
                }
            },
            {
                xtype: 'gui-bundles-grid',
                region: 'center',
                border: true
            }
        ];

        Phlexible.gui.BundlesMainPanel.superclass.initComponent.call(this);
    },

    loadParams: function () {

    }
});

Ext.reg('gui-bundles', Phlexible.gui.BundlesMainPanel);