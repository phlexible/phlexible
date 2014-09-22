Phlexible.elementfinder.ElementFinderConfigWindow = Ext.extend(Ext.Window, {
    strings: Phlexible.elementfinder.Strings.preview,
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
            fields: ['id', 'version', 'language', 'title', 'icon'],
            id: 'id',
            root: 'items',
            baseParams: {},
            listeners: {
                load: function(store) {
                    this.getComponent(1).getTopToolbar().items.items[2].setText(String.format(this.strings.showing, 1, store.getCount(), store.reader.jsonData.total));
                },
                scope: this
            }
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
            autoExpandColumn: 3,
            emptyText: this.strings.no_match,
            columns: [{
                header: this.strings.id,
                dataIndex: 'id',
                width: 50
            },{
                header: this.strings.version,
                dataIndex: 'version',
                width: 50
            },{
                header: this.strings.language,
                dataIndex: 'language',
                width: 50,
                renderer: function(v) {
                    return Phlexible.inlineIcon("p-flags-" + v + "-icon");
                }
            },{
                header: this.strings.title,
                dataIndex: 'title',
                renderer: function(v, md, r) {
                    return '<img src="' + r.data.icon + '" width="18" height="18" style="vertical-align: middle;" /> ' + v;
                }
            }],
            tbar: [{
                text: this.strings.preview,
                handler: function() {
                    this.getComponent(1).getStore().baseParams = this.getComponent(0).getValues();
                    this.getComponent(1).getStore().reload();
                },
                scope: this
            },'->',{
                xtype: 'tbtext',
                text: '&nbsp;'
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
