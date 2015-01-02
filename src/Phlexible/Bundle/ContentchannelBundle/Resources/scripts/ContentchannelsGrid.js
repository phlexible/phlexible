Ext.ns('Phlexible.contentchannels');

Phlexible.contentchannels.ContentchannelsGrid = Ext.extend(Ext.grid.GridPanel, {
    strings: Phlexible.contentchannels.Strings,
    title: Phlexible.contentchannels.Strings.component_name,
    autoExpandColumn: 1,
    disabled: true,

    initComponent: function () {
        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('contentchannels_list'),
            root: 'contentchannels',
            id: 'id',
            fields: ['id', 'unique_id', 'renderer_classname', 'title', 'icon', 'template_folder', 'comment'],
            autoLoad: true,
            listeners: {
                load: {
                    fn: function () {
                        this.enable();
                    },
                    scope: this
                }
            }
        });

        this.selModel = new Ext.grid.RowSelectionModel({
            singleSelect: true,
            listeners: {
                selectionchange: {
                    fn: function (sm) {
                        if (sm.getSelected()) {
                            this.getTopToolbar().items.items[2].enable();
                        }
                        else {
                            this.getTopToolbar().items.items[2].disable();
                        }
                    },
                    scope: this
                }
            }
        });

        this.columns = [
            {
                header: this.strings.id,
                dataIndex: 'id',
                sortable: true,
                width: 50,
                hidden: true
            },
            {
                header: this.strings.title,
                dataIndex: 'title',
                sortable: true,
                width: 200
            }
        ];

        this.tbar = [
            {
                text: this.strings.add,
                iconCls: 'p-contentchannel-add-icon',
                handler: this.newContentchannel,
                scope: this
            }
        ];

        this.on({
            rowdblclick: {
                fn: function (grid, rowIndex) {
                    var r = grid.getStore().getAt(rowIndex);

                    if (!r) {
                        return;
                    }

                    this.fireEvent('contentchannel_select', r);
                },
                scope: this
            }
        });

        Phlexible.contentchannels.ContentchannelsGrid.superclass.initComponent.call(this);
    },

    newContentchannel: function () {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('contentchannels_create'),
            success: function (response) {
                var data = Ext.decode(response.responseText);
                if (data.success) {
                    this.store.on('load', function (id) {
                        var r = this.store.getById(id);
                        var index = this.store.indexOf(r);
                        this.selModel.selectRange(index);

                        this.fireEvent('create', id);
                    }.createDelegate(this, [data.id]));
                    this.store.reload();
                }
                else {
                    Ext.Msg.alert('Failure', data.msg);
                }
            },
            scope: this

        });
    },

    saveContentchannel: function () {
        var mr = this.store.getModifiedRecords();

        var data = [];
        Ext.each(mr, function (r) {
            data.push(r.data);
        }, this);

        Ext.Ajax.request({
            url: Phlexible.Router.generate('contentchannels_save'),
            params: {
                data: Ext.encode(data)
            },
            success: function (response) {
                var result = Ext.decode(response.responseText);

                if (result.success) {
                    this.store.reload();
                }
                else {
                    Phlexible.error(result.msg);
                }
            },
            scope: this
        });
    }
});

Ext.reg('contentchannels-contentchannelsgrid', Phlexible.contentchannels.ContentchannelsGrid);
