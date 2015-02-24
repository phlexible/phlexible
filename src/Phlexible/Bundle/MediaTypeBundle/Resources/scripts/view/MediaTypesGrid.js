Ext.provide('Phlexible.mediatype.MediaTypesGrid');

Ext.require('Phlexible.mediatype.model.MediaType');

Phlexible.mediatype.MediaTypesGrid = Ext.extend(Ext.grid.GridPanel, {
    title: Phlexible.mediatype.Strings.media_types,
    strings: Phlexible.mediatype.Strings,
    iconCls: 'p-mediatype-component-icon',
    loadMask: true,
    stripeRows: true,

    initComponent: function () {
        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('mediatypes_list'),
            root: 'mediatypes',
            id: 'id',
            totalProperty: 'totalCount',
            fields: Phlexible.mediatype.model.MediaType,
            autoLoad: true,
            sortInfo: {field: 'key', direction: 'ASC'}
        });

        this.selModel = new Ext.grid.RowSelectionModel({
            singleSelect: true,
            listeners: {
                selectionchange: function (sm) {
                    var r = sm.getSelected();
                    this.fireEvent('mediaTypeChange', r);
                },
                scope: this
            }
        });

        this.columns = [
            {
                header: this.strings.id,
                dataIndex: 'id',
                sortable: false,
                hidden: true,
                width: 220
            },
            {
                id: 'key',
                header: this.strings.key,
                dataIndex: 'key',
                sortable: true,
                width: 100
            },
            {
                header: 'de', //this.strings.type,
                dataIndex: 'de',
                sortable: true,
                width: 250
            },
            {
                header: 'en', //this.strings.type,
                dataIndex: 'en',
                sortable: true,
                width: 250
            },
            {
                header: this.strings.mimetypes,
                dataIndex: 'mimetypes',
                sortable: false,
                width: 100,
                renderer: function (m) {
                    if (!Ext.isArray(m) || !m.length) {
                        return 'No mimetypes';
                    }
                    if (m.length > 1) return m.length + ' mimetypes';
                    return m.length + ' mimetype';
                }
            },
            {
                header: '16',
                dataIndex: 'icon16',
                sortable: false,
                width: 30,
                renderer: this.iconRenderer
            },
            {
                header: '32',
                dataIndex: 'icon32',
                sortable: false,
                width: 30,
                renderer: this.iconRenderer
            },
            {
                header: '48',
                dataIndex: 'icon48',
                sortable: false,
                width: 30,
                renderer: this.iconRenderer
            },
            {
                header: '256',
                dataIndex: 'icon256',
                sortable: false,
                width: 30,
                renderer: this.iconRenderer
            }
        ];

        this.tbar = [
            {
                text: this.strings.reload,
                iconCls: 'x-tbar-loading',
                handler: function () {
                    this.store.reload();
                },
                scope: this
            }
        ];

        Phlexible.mediatype.MediaTypesGrid.superclass.initComponent.call(this);
    },

    iconRenderer: function (k) {
        var icon = k ? 'found' : 'missing';
        return Phlexible.inlineIcon('p-mediatype-' + icon + '-icon', {alt: k});
    }
});

Ext.reg('mediatype-mediatypesgrid', Phlexible.mediatype.MediaTypesGrid);