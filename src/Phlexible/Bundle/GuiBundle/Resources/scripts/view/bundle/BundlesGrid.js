Ext.provide('Phlexible.gui.BundlesGrid');

Ext.require('Phlexible.gui.model.Bundle');

Phlexible.gui.BundlesGrid = Ext.extend(Ext.grid.GridPanel, {
    strings: Phlexible.gui.Strings,
    loadMask: true,
    hint: false,
    cls: 'p-gui-component-grid',

    initComponent: function () {
        this.addEvents(
            'dirty'
        );

        // create the Data Store
        this.store = new Ext.data.GroupingStore({
            // load using HTTP
            proxy: new Ext.data.HttpProxy({
                url: Phlexible.Router.generate('gui_bundles')
            }),
            baseParams: {
                required: 0
            },
            // the return will be XML, so lets set up a reader
            reader: new Ext.data.JsonReader({
                id: 'id'
            }, Phlexible.gui.model.Bundle),

            autoLoad: true,
            sortInfo: {field: 'id', direction: 'ASC'},
            listeners: {
                load: function () {
                    if (this.filterData) {
                        this.setFilterData(this.filterData);
                    }
                },
                scope: this
            }
        });

        this.columns = [
            {
                header: this.strings.bundle,
                width: 250,
                dataIndex: 'id',
                renderer: function (id, md, r) {
                    return Phlexible.inlineIcon(r.get('icon')) + ' ' + id;
                },
                resizable: false
            },
            {
                header: this.strings.package,
                width: 100,
                dataIndex: 'package',
                resizable: false
            },
            {
                header: this.strings.classname,
                width: 400,
                dataIndex: 'classname',
                resizable: false
            },
            {
                header: this.strings.path,
                width: 500,
                dataIndex: 'path',
                hidden: true,
                resizable: false
            }
        ];

        this.view = new Ext.grid.GroupingView({
            emptyText: Phlexible.gui.Strings.no_bundles,
            deferEmptyText: true,
            hideGroupedColumn: true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? Phlexible.gui.Strings.bundles : Phlexible.gui.Strings.bundle]})'
        });

        Phlexible.gui.BundlesGrid.superclass.initComponent.call(this);
    },

    setFilterData: function (data) {
        this.filterData = data;
        this.store.clearFilter();
        this.store.filterBy(function (record, id, data) {
            if (data.packages.length && data.packages.indexOf(record.data['package']) === -1) {
                return false;
            }

            if (data.filter) {
                var regex = new RegExp(data.filter, 'i');

                if (!record.data['id'].match(regex)) {
                    return false;
                }
            }

            return true;
        }.createDelegate(this, [data], true));
    }
});

Ext.reg('gui-bundles-grid', Phlexible.gui.BundlesGrid);