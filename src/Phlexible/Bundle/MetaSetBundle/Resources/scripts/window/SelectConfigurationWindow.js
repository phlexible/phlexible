Ext.provide('Phlexible.metasets.SelectConfigurationWindow');

Phlexible.metasets.SelectConfigurationWindow = Ext.extend(Ext.Window, {
    title: Phlexible.metasets.Strings.configure_select,
    strings: Phlexible.metasets.Strings,
    width: 300,
    height: 400,
    layout: 'fit',
    modal: true,

    initComponent: function() {
        var actions = new Ext.ux.grid.RowActions({
            header: this.strings.actions,
            width: 40,
            actions: [
                {
                    iconCls: 'p-metaset-delete-icon',
                    tooltip: this.strings.remove_value,
                    callback: this.deleteValue,
                    scope: this
                }
            ]
        });

        var values = [];
        if (this.options) {
            Ext.each(this.options.split(','), function(value) {
                values.push([value]);
            });
        }

        this.items = [{
            xtype: 'editorgrid',
            border: false,
            autoExpandColumn: 'value',
            viewConfig: {
                deferEmptyText: false,
                emptyText: this.strings.use_add,
                stripeRows: true
            },
            store: new Ext.data.SimpleStore({
                fields: ['value'],
                data: values
            }),
            columns: [{
                id: 'value',
                header: this.strings.value,
                dataIndex: 'value',
                editor: new Ext.form.TextField()
            },
                actions
            ],
            plugins: [actions],
            tbar: [{
                text: this.strings.add_value,
                iconCls: 'p-metaset-add-icon',
                handler: this.addValue,
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
            text: this.strings.store,
            handler: function() {
                var options = [],
                    records = this.getComponent(0).getStore().getRange();
                if (!records.length) {
                    Ext.MessageBox.alert(this.strings.failure, this.strings.add_at_least_one_value);
                    return;
                }
                Ext.each(records, function(r) {
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