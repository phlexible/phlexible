Phlexible.elementtypes.ElementtypeUsage = Ext.extend(Ext.grid.GridPanel, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.usage,
    autoExpandColumn: 2,

    viewConfig: {
        emptyText: Phlexible.elementtypes.Strings.no_usage
    },

    initComponent: function () {

        this.store = new Ext.data.JsonStore({
            url: '',
            root: 'list',
            totalProperty: 'total',
            autoLoad: false,
            sortInfo: {
                field: 'title',
                direction: 'ASC'
            },
            fields: ['type', 'id', 'title', 'latest_version']
        });

        this.columns = [
            {
                header: this.strings.type,
                width: 100,
                sortable: true,
                dataIndex: 'type'
            },
            {
                header: 'ID',
                width: 50,
                sortable: true,
                dataIndex: 'id'
            },
            {
                header: this.strings.title,
                sortable: true,
                dataIndex: 'title'
            },
            {
                header: this.strings.latest_version,
                sortable: true,
                dataIndex: 'latest_version'
            }
        ];

        Phlexible.elementtypes.ElementtypeUsage.superclass.initComponent.call(this);
    },

    empty: function () {
        this.store.removeAll();
    },

    load: function (id, title, version, type) {
        this.store.proxy.conn.url = Phlexible.Router.generate('elementtypes_usage', {id: id});
        this.store.reload();
    }
});

Ext.reg('elementtypes-usage', Phlexible.elementtypes.ElementtypeUsage);