Phlexible.metasets.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.metasets.Strings.metasets,
    strings: Phlexible.metasets.Strings,
    layout: 'border',
    iconCls: 'p-metaset-component-icon',

    initComponent: function () {
        var metaFields = new Phlexible.metasets.Fields();

        // Create RowActions Plugin
        var actions = new Ext.ux.grid.RowActions({
            header: '_actions',
            width: 40,
            actions: [
                {
                    iconCls: 'p-metaset-delete-icon',
                    tooltip: '_delete',
                    callback: this.deleteField.createDelegate(this),
                    scope: this
                },
                {
                    iconCls: 'p-metaset-edit-icon',
                    tooltip: '_configure',
                    callback: this.configureField.createDelegate(this),
                    scope: this
                }
            ]
        });

        this.items = [
            {
                xtype: 'grid',
                region: 'west',
                width: 200,
                loadMask: true,
                viewConfig: {
                    forceFit: true
                },
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('metasets_sets_list'),
                    root: 'sets',
                    fields: ['id', 'title'],
                    autoLoad: true,
                    sortInfo: {
                        field: 'title',
                        direction: 'ASC'
                    }
                }),
                columns: [
                    {
                        header: '_id',
                        dataIndex: 'id',
                        hidden: true
                    },
                    {
                        header: '_title',
                        dataIndex: 'title'
                    }
                ],
                tbar: [
                    {
                        text: '_add',
                        iconCls: 'p-metaset-add-icon',
                        handler: function () {
                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('metasets_sets_create'),
                                success: function (response) {
                                    var data = Ext.decode(response.responseText);

                                    if (data.success) {
                                        Phlexible.success(data.msg);

                                        this.getComponent(0).store.reload();
                                    } else {
                                        Ext.MessageBox.alert('Failure', data.msg);
                                    }
                                },
                                scope: this
                            });
                        },
                        scope: this
                    }
                ],
                listeners: {
                    rowdblclick: function (grid, rowIndex) {
                        var id = grid.store.getAt(rowIndex).get('id');
                        this.getComponent(1).setId = id;
                        this.getComponent(1).enable();
                        this.getComponent(1).store.baseParams.id = id;
                        this.getComponent(1).store.load();
                    },
                    scope: this
                }
            },
            {
                xtype: 'editorgrid',
                region: 'center',
                disabled: true,
                enableDragDrop: true,
                ddGroup: 'metasetitem_reorder',
                loadMask: {
                    text: 'blubb'
                },
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('metasets_sets_fields'),
                    baseParams: {
                        id: ''
                    },
                    root: 'values',
                    fields: ['key', 'type', 'required', 'synchronized', 'readonly', 'options']
                }),
                sm: new Ext.grid.RowSelectionModel(),
                columns: [
                    {
                        header: '_name',
                        dataIndex: 'key',
                        width: 200,
                        editor: new Ext.form.TextField()
                    },
                    {
                        header: '_type',
                        dataIndex: 'type',
                        width: 200,
                        editor: new Ext.form.ComboBox({
                            store: new Ext.data.SimpleStore({
                                fields: ['type', 'text'],
                                data: metaFields.getFields()
                            }),
                            displayField: 'text',
                            valueField: 'type',
                            mode: 'local',
                            typeAhead: false,
                            triggerAction: 'all',
                            selectOnFocus: true,
                            editable: false
                        })
                    },
                    {
                        header: '_required',
                        dataIndex: 'required',
                        width: 60,
                        editor: new Ext.form.NumberField()
                    },
                    {
                        header: '_synchronized',
                        dataIndex: 'synchronized',
                        width: 85,
                        editor: new Ext.form.NumberField()
                    },
                    {
                        header: '_readonly',
                        dataIndex: 'readonly',
                        width: 75,
                        editor: new Ext.form.NumberField()
                    },
                    {
                        header: '_options',
                        dataIndex: 'options',
                        width: 200,
                        //editor: new Ext.form.TextField(),
                        hidden: true
                    },
                    actions
                ],
                plugins: [
                    actions
                ],
                tbar: [
                    {
                        text: 'Add field',
                        iconCls: 'p-metaset-add-icon',
                        handler: function () {
                            var r = new Ext.data.Record({key: '', type: 'textfield'});
                            r.set('key', 'key-' + r.id);
                            this.getComponent(1).store.add(r);

                        },
                        scope: this
                    },
                    '-',
                    {
                        text: 'Save',
                        iconCls: 'p-metaset-save-icon',
                        handler: function () {
                            var params = [];

                            for (var i = 0; i < this.getComponent(1).store.getCount(); i++) {
                                var r = this.getComponent(1).store.getAt(i);
                                params.push({
                                    key: r.data.key,
                                    type: r.data.type,
                                    required: r.data.required,
                                    'synchronized': r.data['synchronized'],
                                    readonly: r.data['readonly'],
                                    options: r.data.options
                                });
                            }

                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('metasets_sets_save'),
                                params: {
                                    id: this.getComponent(1).setId,
                                    data: Ext.encode(params)
                                },
                                success: function (response) {
                                    var data = Ext.decode(response.responseText);

                                    if (data.success) {
                                        this.getComponent(1).store.reload();

                                        Phlexible.success(data.msg);
                                    } else {
                                        Ext.MessageBox.alert('Success', data.msg);
                                    }
                                },
                                scope: this
                            });
                        },
                        scope: this
                    }
                ],
                listeners: {
                    render: function (grid) {
                        this.ddrow = new Ext.ux.dd.GridReorderDropTarget(grid, {
                            copy: false
                        });
                    },
                    scope: this
                }
            }
        ];

        Phlexible.metasets.MainPanel.superclass.initComponent.call(this);
    },

    configureField: function(grid, record) {
        if (record.get('type') === 'suggest') {
            var w = new Phlexible.metasets.SuggestConfigurationWindow();
            w.show();
        }
        else if (record.get('type') === 'select') {
            var w = new Phlexible.metasets.SelectConfigurationWindow();
            w.show();
        }
    },

    deleteField: function (grid, record) {
        grid.getStore().remove(record);
    }
});

Ext.reg('metasets-main', Phlexible.metasets.MainPanel);