Phlexible.metasets.SelectConfigurationWindow = Ext.extend(Ext.Window, {
    title: '_select',
    width: 300,
    height: 400,
    layout: 'fit',
    modal: true,

    initComponent: function() {
        var actions = new Ext.ux.grid.RowActions({
            header: '_actions',
            width: 40,
            actions: [
                {
                    iconCls: 'p-metaset-delete-icon',
                    tooltip: '_remove_value',
                    callback: this.deleteValue,
                    scope: this
                }
            ]
        });

        var values = [];
        Ext.each(this.options.split(','), function(value) {
            values.push([value]);
        });

        this.items = [{
            xtype: 'editorgrid',
            border: false,
            autoExpandColumn: 'value',
            store: new Ext.data.SimpleStore({
                fields: ['value'],
                data: values
            }),
            columns: [{
                id: 'value',
                header: '_value',
                dataIndex: 'value',
                editor: new Ext.form.TextField()
            },
                actions
            ],
            plugins: [actions],
            tbar: [{
                text: '_add_value',
                iconCls: 'p-metaset-add-icon',
                handler: this.addValue,
                scope: this
            }]
        }];

        this.buttons = [{
            text: '_cancel',
            handler: function() {
                this.close();
            },
            scope: this
        },{
            text: '_store',
            handler: function() {
                var options = [];
                Ext.each(this.getComponent(0).getStore().getRange(), function(r) {
                    options.push(r.get('value'));
                });
                this.fireEvent('store', options.join(','));
                this.close();
            },
            scope: this
        }];

        Phlexible.metasets.SuggestConfigurationWindow.superclass.initComponent.call(this);
    },

    addValue: function() {
        this.getComponent(0).getStore().add(new Ext.data.Record({value: Ext.id(null, 'value-')}));
    },

    deleteValue: function(grid, record) {
        grid.getStore().remove(record);
    }
});