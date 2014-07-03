Phlexible.elements.HistoryWindow = Ext.extend(Ext.Window, {
    title: Phlexible.elements.Strings.history,
    iconCls: 'p-element-tab_history-icon',
    width: 980,
    height: 600,
    layout: 'border',
    border: false,
    modal: true,
    maximizable: true,
    constrainHeader: true,

    initComponent: function () {
        this.items = [
            {
                xtype: 'elements-historyfilter',
                region: 'west',
                width: 150,
                header: false,
                listeners: {
                    applysearch: {
                        fn: function (values) {
                            var store = this.getComponent(1).getStore();
                            for (var i in values) {
                                if (values[i]) {
                                    store.baseParams[i] = values[i];
                                }
                                else if (store.baseParams[i]) {
                                    store.baseParams[i] = '';
                                }
                            }
                            store.reload();
                        },
                        scope: this
                    }
                }
            },
            {
                xtype: 'elements-historygrid',
                region: 'center',
                header: false,
                listeners: {
                    render: {
                        fn: function (c) {
                            c.getStore().load();
                        }
                    }
                }
            }
        ];

        Phlexible.elements.HistoryWindow.superclass.initComponent.call(this);
    }
});
