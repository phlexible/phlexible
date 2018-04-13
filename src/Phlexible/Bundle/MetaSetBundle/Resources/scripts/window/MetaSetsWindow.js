Ext.provide('Phlexible.metasets.MetaSetsWindow');

Phlexible.metasets.MetaSetsWindow = Ext.extend(Ext.Window, {
    title: Phlexible.metasets.Strings.metasets,
    strings: Phlexible.metasets.Strings,
    iconCls: 'p-metaset-component-icon',
    width: 400,
    height: 300,
    layout: 'fit',
    modal: true,
    constrainHeader: true,

    baseParams: {},

    urls: {},

    initComponent: function () {
        if (!this.urls.list || !this.urls.available || !this.urls.save) {
            throw 'Missing url config';
        }

        // Create RowActions Plugin
        var actions = new Ext.ux.grid.RowActions({
            header: this.strings.actions,
            width: 40,
            actions: [
                {
                    iconCls: 'p-metaset-delete-icon',
                    tooltip: this.strings.remove_metaset,
                    callback: this.removeMetaSet.createDelegate(this),
                    scope: this
                }
            ]
        });

        var storeConfig = {
                fields: ['id', 'name'],
                id: 'id',
                baseParams: this.baseParams,
                autoLoad: true,
                sortInfo: {field: "name", direction: "ASC"}
            },
            store;
        if (typeof this.urls.list === 'function') {
            storeConfig.data = this.urls.list(this);
        } else {
            storeConfig.url = this.urls.list;
            storeConfig.root = 'sets';
        }
        store = new Ext.data.JsonStore(storeConfig);

        this.items = [
            {
                xtype: 'grid',
                border: false,
                viewConfig: {
                    forceFit: true
                },
                store: store,
                columns: [
                    {
                        header: this.strings.metaset,
                        dataIndex: 'name'
                    },
                    actions
                ],
                sm: new Ext.grid.RowSelectionModel({
                    singleSelect: true
                }),
                plugins: [actions]
            }
        ];

        this.tbar = [
            {
                xtype: 'combo',
                store: new Ext.data.JsonStore({
                    url: this.urls.available,
                    fields: ['id', 'name'],
                    root: 'sets',
                    id: 'id',
                    baseParams: this.baseParams,
                    sortInfo: {field: "name", direction: "ASC"},
                    listeners: {
                        load: function(store, records) {
                            Ext.each(records, function(record) {
                                if (this.getComponent(0).getStore().find('id', record.get('id')) !== -1) {
                                    store.remove(record);
                                }
                            }, this);
                        },
                        scope: this
                    }
                }),
                emptyText: this.strings.select_metaset,
                valueField: 'id',
                displayField: 'name',
                mode: 'remote',
                triggerAction: 'all',
                editable: false
            },
            {
                text: this.strings.add,
                iconCls: 'p-metaset-add-icon',
                handler: this.addMetaSet,
                scope: this
            }
        ];

        this.buttons = [{
            text: this.strings.cancel,
            handler: this.close,
            scope: this
        },{
            text: this.strings.save,
            iconCls: 'p-metaset-save-icon',
            handler: this.save,
            scope: this
        }];

        Phlexible.metasets.MetaSetsWindow.superclass.initComponent.call(this);
    },

    addMetaSet: function () {
        var id = this.getTopToolbar().items.items[0].getValue(),
            name = this.getTopToolbar().items.items[0].getRawValue();

        if (!id || !id.length) {
            return;
        }

        this.getComponent(0).getStore().add(new Ext.data.Record({id: id, name: name}));

        var combo = this.getTopToolbar().items.items[0],
            idx = combo.store.find('id', id);
        if (idx !== -1) {
            combo.store.removeAt(idx);
        }
        combo.setValue(null);
    },

    removeMetaSet: function (grid, record) {
        grid.getStore().remove(record);

        var combo = this.getTopToolbar().items.items[0];
        combo.store.addSorted(new Ext.data.Record({id: record.get('id'), name: record.get('name')}));
    },

    save: function() {
        var params = Phlexible.clone(this.baseParams);
        var metas = [];
        params.ids = [];
        Ext.each(this.getComponent(0).getStore().getRange(), function(record) {
            params.ids.push(record.get('id'));
            metas.push(record.data);
        });

        if (typeof this.urls.save === 'function') {
            this.urls.save(params, this);
            this.fireEvent('updatesets', metas);
            this.close();
            return;
        }

        params.ids = params.ids.join(',');

        Ext.Ajax.request({
            url: this.urls.save,
            params: params,
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    Phlexible.success(data.msg);

                    this.fireEvent('savesets');

                    this.close();
                } else {
                    Ext.MessageBox.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    }
});
