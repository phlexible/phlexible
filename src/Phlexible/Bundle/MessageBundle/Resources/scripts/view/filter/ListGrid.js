Ext.provide('Phlexible.messages.filter.ListGrid');

Ext.require('Phlexible.messages.model.Filter');
Ext.require('Ext.ux.grid.RowActions');

Phlexible.messages.filter.ListGrid = Ext.extend(Ext.grid.GridPanel, {
    title: Phlexible.messages.Strings.filters,
    strings: Phlexible.messages.Strings,
    autoExpandColumn: 'filterTitle',
    loadMask: true,
    hideMode: 'offsets',
    viewConfig: {
        emptyText: Phlexible.messages.Strings.no_filters,
        deferEmptyText: false
    },

    initComponent: function () {
        // Create RowActions Plugin
        var actions = new Ext.ux.grid.RowActions({
            header: this.strings.actions,
            width: 40,
            actions: [
                {
                    iconCls: 'p-message-delete-icon',
                    tooltip: this.strings["delete"],
                    callback: this.deleteFilter.createDelegate(this),
                    scope: this
                }
            ]
        });

        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('messages_filters'),
            id: 'id',
            fields: Phlexible.messages.model.Filter,
            autoLoad: true
        });

        this.selModel = new Ext.grid.RowSelectionModel();

        this.columns = [
            {
                id: 'filterTitle',
                header: this.strings.title,
                dataIndex: 'title',
                sortable: true,
                width: 100
            },
            actions
        ];

        this.plugins = [actions];

        this.tbar = [
            {
                text: this.strings.add,
                iconCls: 'p-message-add-icon',
                handler: this.addFilter,
                scope: this
            }
        ];

        this.addListener({
            rowdblclick: this.filterChange,
            scope: this
        });

        Phlexible.messages.filter.ListGrid.superclass.initComponent.call(this);
    },

    addFilter: function () {
        Ext.MessageBox.prompt(Phlexible.messages.Strings.add, Phlexible.messages.Strings.add_filter, function (btn, title) {
            if (btn === 'ok') {
                Ext.Ajax.request({
                    url: Phlexible.Router.generate('messages_filter_create'),
                    params: {
                        title: title
                    },
                    success: function (response) {
                        var result = Ext.decode(response.responseText);

                        if (result.success) {
                            Phlexible.success(result.msg);
                            this.store.reload();
                        } else {
                            Phlexible.failure(result.msg);
                        }
                    },
                    scope: this
                });
            }
        }, this);
    },

    filterChange: function (grid, rowIndex, event) {
        var r = grid.store.getAt(rowIndex);
        this.fireEvent('filterChange', r);
    },

    deleteFilter: function (grid, record) {
        Ext.MessageBox.confirm(this.strings['delete'], String.format(this.strings.delete_filter, record.get('title')), function (btn) {
            if (btn != 'yes') {
                return;
            }
            Ext.Ajax.request({
                url: Phlexible.Router.generate('messages_filter_delete', {id: record.get('id')}),
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    if (data.success) {
                        Phlexible.success(data.msg);
                        this.store.reload();
                        this.fireEvent('filterDeleted');
                    } else {
                        Phlexible.failure(data.msg);
                    }
                },
                scope: this
            });
        }, this);
    }
});

Ext.reg('messages-filter-listgrid', Phlexible.messages.filter.ListGrid);
