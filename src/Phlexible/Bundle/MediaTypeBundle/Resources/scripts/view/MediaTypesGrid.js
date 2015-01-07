Ext.provide('Phlexible.mediatype.MediaTypesGrid');

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

        this.addListener({
            rowdblclick: function (grid, index) {
                var r = grid.getStore().getAt(index);

                if (!r) {
                    return;
                }

                var key = r.get('key');

                var w = new Ext.Window({
                    title: String.format(this.strings.icons_for, r.get('en')),
                    width: 420,
                    height: 320,
                    bodyStyle: 'background: white; background: linear-gradient(135deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 0 0, linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 15px 15px, linear-gradient(135deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 15px 15px, linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 0 0, lightgray; background-size: 30px 30px; padding: 5px;',
                    modal: true,
                    html: '<table><tr>' +
                        '<td align="center" valign="bottom">' +
                        '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes16/' + key + '.gif') + '" width="16" height="16" />' +
                        '</td>' +
                        '<td align="center" valign="bottom">' +
                        '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes32/' + key + '.gif') + '" width="32" height="32" />' +
                        '</td>' +
                        '<td align="center" valign="bottom">' +
                        '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes48/' + key + '.gif') + '" width="48" height="48" />' +
                        '</td>' +
                        '<td align="center" valign="bottom">' +
                        '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes256/' + key + '.gif') + '" width="256" height="256" />' +
                        '</td>' +
                        '</tr><tr>' +
                        '<td align="center">16x16</td>' +
                        '<td align="center">32x32</td>' +
                        '<td align="center">48x48</td>' +
                        '<td align="center">256x256</td>' +
                        '</tr></table>'
                });
                w.show();
            },
            scope: this
        });

        Phlexible.mediatype.MediaTypesGrid.superclass.initComponent.call(this);
    },

    iconRenderer: function (k) {
        var icon = k ? 'found' : 'missing';
        return Phlexible.inlineIcon('p-mediatype-' + icon + '-icon', {alt: k});
    }
});

Ext.reg('mediatype-mediatypesgrid', Phlexible.mediatype.MediaTypesGrid);