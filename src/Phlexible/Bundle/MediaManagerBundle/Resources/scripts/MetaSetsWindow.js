Phlexible.mediamanager.MetaSetsWindow = Ext.extend(Ext.Window, {
    title: 'Meta sets',
    iconCls: 'p-metasets-component-icon',
    width: 400,
    height: 300,
    layout: 'fit',

    baseParams: {},

    urls: {},

    initComponent: function () {

        if (!this.urls.list || !this.urls.available || !this.urls.add || !this.urls.remove) {
            throw 'Missing url config';
        }

        this.items = [
            {
                xtype: 'grid',
                viewConfig: {
                    forceFit: true
                },
                store: new Ext.data.JsonStore({
                    url: this.urls.list,
                    fields: ['set_id', 'name'],
                    root: 'sets',
                    baseParams: this.baseParams,
                    autoLoad: true
                }),
                columns: [
                    {
                        header: 'Set',
                        dataIndex: 'name'
                    }
                ],
                sm: new Ext.grid.RowSelectionModel({
                    singleSelect: true,
                    listeners: {
                        selectionchange: {
                            fn: function (sm) {
                                var selections = sm.getSelections();

                                if (selections.length === 1) {
                                    this.getTopToolbar().items.items[3].enable();
                                } else {
                                    this.getTopToolbar().items.items[3].disable();
                                }
                            },
                            scope: this
                        }
                    }
                })
            }
        ];

        this.tbar = [
            {
                xtype: 'combo',
                store: new Ext.data.JsonStore({
                    url: this.urls.available,
                    fields: ['set_id', 'name'],
                    root: 'sets',
                    id: 'set_id',
                    baseParams: this.baseParams
                }),
                valueField: 'set_id',
                displayField: 'name',
                mode: 'remote',
                triggerAction: 'all'
            },
            {
                text: 'Add',
                iconCls: 'p-metasets-add-icon',
                handler: function () {
                    var set_id = this.getTopToolbar().items.items[0].getValue();

                    if (!set_id || !set_id.length) {
                        return;
                    }

                    Ext.Ajax.request({
                        url: this.urls.add,
                        params: Ext.apply({}, {set_id: set_id}, this.baseParams),
                        success: function (response) {
                            var data = Ext.decode(response.responseText);

                            if (data.success) {
                                var combo = this.getTopToolbar().items.items[0];
                                var r = combo.store.getById(set_id);
                                combo.store.remove(r);

                                this.getComponent(0).store.reload();

                                Phlexible.success(data.msg);

                                this.fireEvent('addset');
                            } else {
                                Ext.MessageBox.alert('Failure', data.msg);
                            }
                        },
                        scope: this
                    });
                },
                scope: this
            },
            '-',
            {
                text: 'Remove',
                iconCls: 'p-metasets-delete-icon',
                disabled: true,
                handler: function () {
                    var r = this.getComponent(0).getSelectionModel().getSelected();
                    var set_id = r.data.set_id;

                    var newRecord = new Ext.data.Record({
                        set_id: set_id,
                        name: r.data.name
                    });
                    var combo = this.getTopToolbar().items.items[0];
                    var r = combo.store.insert(0, newRecord);

                    Ext.Ajax.request({
                        url: this.urls.remove,
                        params: Ext.apply({}, {set_id: set_id}, this.baseParams),
                        success: function (response) {
                            var data = Ext.decode(response.responseText);

                            if (data.success) {
                                this.getComponent(0).store.reload();

                                Phlexible.success(data.msg);

                                this.fireEvent('removeset');
                            } else {
                                Ext.MessageBox.alert('Failure', data.msg);
                            }
                        },
                        scope: this
                    });
                },
                scope: this
            }
        ];

        Phlexible.mediamanager.MetaSetsWindow.superclass.initComponent.call(this);
    }
});
