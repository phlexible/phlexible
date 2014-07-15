Phlexible.metasets.SelectConfigurationWindow = Ext.extends(Ext.Window, {
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
                    tooltip: '_delete',
                    callback: function() {},
                    scope: this
                }
            ]
        });

        var values = [];
        Ext.each(record.get('options').split(','), function(value) {
            values.push(value);
        });

        this.items = [{
            xtype: 'grid',
            border: false,
            autoExpandColumn: 'value',
            store: new Ext.data.SimpleStore({
                fields: ['value'],
                data: values,
            }),
            columns: [{
                id: 'value',
                header: '_value',
                dataIndex: 'value'
            },
                actions
            ],
            plugins: [actions],
            tbar: [{
                text: '_add',
                iconCls: 'p-metaset-add-icon',
                handler: function() {

                }
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

            },
            scope: this
        }];

        Phlexible.metasets.SuggestConfigurationWindow.superclass.initComponent.call(this);
    }
});