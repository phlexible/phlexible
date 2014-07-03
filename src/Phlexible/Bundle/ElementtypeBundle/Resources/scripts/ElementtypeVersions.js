Phlexible.elementtypes.ElementtypeVersions = Ext.extend(Ext.grid.GridPanel, {
    title: Phlexible.elementtypes.Strings.versions,
    strings: Phlexible.elementtypes.Strings,
    border: false,
    autoScroll: true,
    viewConfig: {
        //forceFit: true,
        emptyText: '_no_versions'//Phlexible.elementtypes.Strings.no_versions
    },
//    autoExpandColumn: 'title',

    initComponent: function () {
        this.store = new Ext.data.JsonStore({
            url: '',//Phlexible.Router.generate('elementtypes_list_versions'),
            id: 'version',
            root: 'versions',
            fields: [
                {name: 'version', type: 'int'},
                'create_user',
                'create_time'
            ],
            sortInfo: {field: 'version', direction: 'DESC'},
            listeners: {
                load: function (store) {
                    var r, index = 0;
                    if (this.elementtypeVersion !== undefined) {
                        index = store.find('version', this.elementtypeVersion);
                    }
                    if (index == -1) {
                        return;
                    }
                    r = store.getAt(index);
                    if (!r) {
                        return;
                    }
                    this.getSelectionModel().selectRecords([r]);
                },
                scope: this
            }
        });

        this.columns = [
            {
                id: 'version',
                header: this.strings.version,
                dataIndex: 'version',
                width: 60
            },
            {
                id: 'create_user',
                header: this.strings.create_user,
                dataIndex: 'create_user',
                width: 200
            },
            {
                id: 'create_time',
                header: this.strings.create_time,
                dataIndex: 'create_time',
                width: 150
            }
        ];

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        this.on({
            rowdblclick: function (grid, rowIndex) {
                var r = grid.getStore().getAt(rowIndex);
                this.fireEvent('loadVersion', this.elementtypeId, this.elementtypeTitle, r.get('version'), this.elementtypeType);
            },
            scope: this
        })
        Phlexible.elementtypes.ElementtypeVersions.superclass.initComponent.call(this);
    },

    empty: function () {
        this.store.removeAll();
    },

    load: function (id, title, version, type) {
        this.elementtypeId = id;
        this.elementtypeVersion = parseInt(version, 10);
        this.elementtypeTitle = title;
        this.elementtypeType = type;

        this.store.proxy.conn.url = Phlexible.Router.generate('elementtypes_list_versions', {id: id});
        this.store.reload();
    },

    isValid: function () {
        return true;
    }
});

Ext.reg('elementtypes-versions', Phlexible.elementtypes.ElementtypeVersions);