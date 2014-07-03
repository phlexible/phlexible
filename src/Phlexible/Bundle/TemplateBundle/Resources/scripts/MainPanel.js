Phlexible.templates.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.templates.Strings.template_browser,
    layout: 'border',
    iconCls: 'p-template-component-icon',

    initComponent: function () {
        this.items = [
            {
                xtype: 'templates-templatesgrid',
                region: 'west',
                width: 300,
                listeners: {
                    rowclick: {
                        fn: function (grid, index) {
                            this.getComponent(1).load(grid.store.getAt(index));
                        },
                        scope: this
                    }
                }
            },
            {
                xtype: 'templates-templatestabs',
                region: 'center'
            }
        ];

        Phlexible.templates.MainPanel.superclass.initComponent.call(this);
    }
});
Ext.reg('templates-main', Phlexible.templates.MainPanel);
