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
        this.items = [{
            xtype: 'elementfinder-finder-config-panel',
            region: 'west',
            width: 500,
            header: false,
            siterootId: this.siterootId,
            values: this.values,
            baseValues: this.baseValues
        },{
            xtype: 'grid',
            region: 'center',
            store: new Ext.data.JsonStore({
                url: Phlexible.Router.generate('elementfinder_catch_preview'),
                fields: ['tree_id', 'eid', 'version', 'language'],
                id: 'tree_id',
                autoLoad: true
            }),
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
        }]

        Phlexible.elementfinder.ElementFinderConfigWindow.superclass.initComponent.call(this);
    }
});
