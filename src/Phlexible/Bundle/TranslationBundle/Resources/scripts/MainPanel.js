Phlexible.translations.MainPanel = Ext.extend(Ext.Panel, {
    closable: true,
    title: Phlexible.translations.Strings.translations,
    layout: 'border',
    cls: 'p-translations-panel',
    iconCls: 'p-translation-component-icon',

    initComponent: function () {

        this.items = [
            {
                xtype: 'translations-filterpanel',
                region: 'west',
                width: 250,
                collapsible: true,
                //split: true,
                listeners: {
                    search: {
                        fn: function (values) {
                            var grid = this.getComponent(1);
                            Ext.apply(grid.store.baseParams, values);
                            grid.store.load({
                                start: 0
                            });
                        },
                        scope: this
                    }
                }
            },
            {
                xtype: 'translations-grid',
                region: 'center'
            }
        ];

        Phlexible.translations.MainPanel.superclass.initComponent.call(this);
    },

    loadParams: function () {
    }
});

Ext.reg('translations-mainpanel', Phlexible.translations.MainPanel);