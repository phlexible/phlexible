Phlexible.elementfinder.ElementFinderConfigWindow = Ext.extend(Ext.Window, {
    strings: Phlexible.elementfinder.Strings,
    title: Phlexible.elementfinder.Strings.finder,
    iconCls: 'p-elementfinder-finder-icon',
    width: 900,
    height: 450,
    layout: 'border',
    modal: true,
    resizable: false,
    border: false,

    initComponent: function () {
        var store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('elementfinder_catch_preview'),
            fields: ['tree_id', 'eid', 'version', 'language'],
            id: 'tree_id',
            baseParams: {}
        });

        this.items = [{
            xtype: 'elementfinder-finder-config-panel',
            region: 'west',
            width: 500,
            header: false,
            siterootId: this.siterootId,
            values: this.values,
            baseValues: this.baseValues,
            listeners: {
                values: function(configPanel, values) {
                    store.baseParams = values;
                    store.load();
                },
                scope: this
            }
        },{
            xtype: 'grid',
            region: 'center',
            store: store,
            columns: [{
                header: '_tree_id',
                dataIndex: 'tree_id'
            },{
                header: '_eid',
                dataIndex: 'eid'
            },{
                header: '_version',
                dataIndex: 'version'
            },{
                header: '_language',
                dataIndex: 'language'
            }],
            tbar: [{
                text: '_preview',
                handler: function() {
                    this.getComponent(1).getStore().baseParams = this.getComponent(0).getValues();
                    this.getComponent(1).getStore().reload();
                },
                scope: this
            }]
        }];

        this.buttons = [{
            text: this.strings.cancel,
            handler: this.close,
            scope: this
        },{
            text: this.strings.store,
            handler: function() {
                var values = this.getComponent(0).getValues();

                if (!values) {
                    return;
                }

                this.fireEvent('set', this, values);
                this.close();
            },
            scope: this
        }];

        Phlexible.elementfinder.ElementFinderConfigWindow.superclass.initComponent.call(this);
    }
});
