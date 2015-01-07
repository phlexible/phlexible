Ext.provide('Phlexible.metasets.SuggestConfigurationWindow');

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
            }),
            tbar: [{
                text: this.strings.create_datasource,
                iconCls: 'p-metaset-add-icon',
                handler: function() {
                    Ext.MessageBox.prompt(this.strings.create_datasource, this.strings.create_datasource_text, function(btn, title) {
                        if (btn !== 'ok') {
                            return;
                        }
                        Ext.Ajax.request({
                            url: Phlexible.Router.generate('datasources_create'),
                            params: {
                                title: title
                            },
                            success: function(response) {
                                var result = Ext.decode(response.responseText);

                                if (result.success) {
                                    this.getComponent(0).getStore().reload();
                                } else {
                                    Phlexible.failure(result.msg);
                                }
                            },
                            scope: this
                        })

                    }, this);
                },
                scope: this
            }]
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