Phlexible.metasets.SuggestConfigurationWindow = Ext.extend(Ext.Window, {
    title: Phlexible.metasets.Strings.configure_suggest,
    strings: Phlexible.metasets.Strings,
    width: 300,
    height: 400,
    layout: 'fit',
    modal: true,

    initComponent: function() {
        this.items = [{
            xtype: 'grid',
            border: false,
            autoExpandColumn: 'datasource',
            store: new Ext.data.JsonStore({
                url: Phlexible.Router.generate('datasources_list'),
                fields: ['id', 'title'],
                id: 'id',
                root: 'datasources',
                autoLoad: true,
                listeners: {
                    load: function(store, records) {
                        if (!this.options) {
                            return;
                        }

                        var row = store.find('id', this.options);

                        if (row === -1) {
                            return;
                        }

                        this.getComponent(0).getSelectionModel().selectRow(row);
                    },
                    scope: this
                }
            }),
            columns: [{
                id: 'datasource',
                header: this.strings.datasource,
                dataIndex: 'title'
            }],
            sm: new Ext.grid.RowSelectionModel({
                singleSelect: true
            })
        }];

        this.buttons = [{
            text: this.strings.cancel,
            handler: function() {
                this.close();
            },
            scope: this
        },{
            text: this.strings.select,
            handler: function() {
                var options = this.getComponent(0).getSelectionModel().getSelected().get('id');
                this.fireEvent('select', options);
                this.close();
            },
            scope: this
        }];

        Phlexible.metasets.SuggestConfigurationWindow.superclass.initComponent.call(this);
    }
});