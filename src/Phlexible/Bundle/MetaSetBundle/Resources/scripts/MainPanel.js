Phlexible.metasets.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.metasets.Strings.metasets,
    strings: Phlexible.metasets.Strings,
    layout: 'border',
    iconCls: 'p-metaset-component-icon',

    initComponent: function () {
        var metaFields = new Phlexible.metasets.Fields();

        // Create RowActions Plugin
        var actions = new Ext.ux.grid.RowActions({
            header: this.strings.actions,
            width: 40,
            actions: [
                {
                    iconCls: 'p-metaset-edit-icon',
                    tooltip: this.strings.configure,
                    hideIndex: 'type!=\'select\'&&type!=\'suggest\'',
                    callback: this.configureField.createDelegate(this),
                    scope: this
                },
                {
                    iconCls: 'p-metaset-delete-icon',
                    tooltip: this.strings.remove_field,
                    callback: this.deleteField.createDelegate(this),
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
                    fields: ['id', 'name'],
                    autoLoad: true,
                    sortInfo: {
                        field: 'name',
                        direction: 'ASC'
                    }
                }),
                columns: [
                    {
                        header: this.strings.id,
                        dataIndex: 'id',
                        hidden: true
                    },
                    {
                        header: this.strings.name,
                        dataIndex: 'name'
                    }
                ],
                tbar: [
                    {
                        text: this.strings.add,
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
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: this.strings.no_fields,
                    stripeRows: true
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
                        header: this.strings.name,
                        dataIndex: 'key',
                        width: 200,
                        editor: new Ext.form.TextField()
                    },
                    {
                        header: this.strings.type,
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
                            editable: false,
                            listeners: {
                                change: function(field, newValue, oldValue) {
                                    if (newValue !== 'select' && newValue !== 'suggest') {
                                        var r = this.getComponent(1).getSelectionModel().getSelected();
                                        r.set('options', '');
                                    }
                                    console.log('change', arguments);
                                },
                                scope: this
                            }

                        })
                    },
                    {
                        header: this.strings.required,
                        dataIndex: 'required',
                        width: 60,
                        renderer: function(v, md) {
                            md.attr += 'style="text-align: center;"';
                            return v ? Phlexible.inlineIcon('p-metaset-checked-icon') : Phlexible.inlineIcon('p-metaset-unchecked-icon');
                        }
                    },
                    {
                        header: this.strings.synchronized,
                        dataIndex: 'synchronized',
                        width: 85,
                        renderer: function(v, md) {
                            md.attr += 'style="text-align: center;"';
                            return v ? Phlexible.inlineIcon('p-metaset-checked-icon') : Phlexible.inlineIcon('p-metaset-unchecked-icon');
                        }
                    },
                    {
                        header: this.strings.readonly,
                        dataIndex: 'readonly',
                        width: 75,
                        renderer: function(v, md) {
                            md.attr += 'style="text-align: center;"';
                            return v ? Phlexible.inlineIcon('p-metaset-checked-icon') : Phlexible.inlineIcon('p-metaset-unchecked-icon');
                        }
                    },
                    {
                        header: this.strings.options,
                        dataIndex: 'options',
                        width: 200,
                        hidden: true
                    },
                    actions
                ],
                plugins: [
                    actions
                ],
                tbar: [
                    {
                        text: this.strings.add_field,
                        iconCls: 'p-metaset-add-icon',
                        handler: this.addField,
                        scope: this
                    },
                    '-',
                    {
                        text: this.strings.save,
                        iconCls: 'p-metaset-save-icon',
                        handler: this.save,
                        scope: this
                    }
                ],
                listeners: {
                    render: function (grid) {
                        this.ddrow = new Ext.ux.dd.GridReorderDropTarget(grid, {
                            copy: false
                        });
                    },
                    celldblclick: function(grid, rowIndex, cellIndex) {
                        var r = grid.getStore().getAt(rowIndex);
                        if (cellIndex === 2) {
                            r.set('required', !r.get('required'));
                        }
                        if (cellIndex === 3) {
                            r.set('synchronized', !r.get('synchronized'));
                        }
                        if (cellIndex === 4) {
                            r.set('readonly', !r.get('readonly'));
                        }
                    },
                    scope: this
                }
            }
        ];

        Phlexible.metasets.MainPanel.superclass.initComponent.call(this);
    },

    addField: function() {
        var r = new Ext.data.Record({key: '', type: 'textfield'});
        r.set('key', 'field-' + r.id);
        this.getComponent(1).store.add(r);
    },

    configureField: function(grid, record) {
        if (record.get('type') === 'suggest') {
            var w = new Phlexible.metasets.SuggestConfigurationWindow({
                options: record.get('options'),
                listeners: {
                    select: function(options) {
                        record.set('options', options);
                    },
                    scope: this
                }
            });
            w.show();
        }
        else if (record.get('type') === 'select') {
            var w = new Phlexible.metasets.SelectConfigurationWindow({
                options: record.get('options'),
                listeners: {
                    store: function(options) {
                        record.set('options', options);
                    },
                    scope: this
                }
            });
            w.show();
        }
    },

    deleteField: function (grid, record) {
        grid.getStore().remove(record);
    },

    save: function () {
        var params = [];

        for (var i = 0; i < this.getComponent(1).store.getCount(); i++) {
            var r = this.getComponent(1).store.getAt(i);

            if (r.get('type') === 'select' && !r.get('options')) {
                Ext.MessageBox.alert(this.strings.failure, this.strings.select_needs_options);
                return;
            }
            if (r.get('type') === 'suggest' && !r.get('options')) {
                Ext.MessageBox.alert(this.strings.failure, this.strings.suggest_needs_options);
                return;
            }

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
    }
});

Ext.reg('metasets-main', Phlexible.metasets.MainPanel);