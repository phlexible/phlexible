Phlexible.frontendmedia.MediaTemplatesWindow = Ext.extend(Ext.Window, {
    title: Phlexible.frontendmedia.Strings.mediatemplate_information,
    strings: Phlexible.frontendmedia.Strings,
    iconCls: 'p-mediamanager-information-icon',
    width: 800,
    height: 400,
    modal: true,
    autoScroll: true,
    layout: 'fit',
    initComponent: function () {
        this.items = [
            {
                xtype: 'grid',
                border: false,
                autoExpandColumn: 1,
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('frontendmediamanager_items'),
                    baseParams: {
                        eid: this.eid,
                        version: this.version,
                        language: this.language,
                        data_id: this.data_id,
                        file_id: this.file_id,
                        file_version: this.file_version
                    },
                    root: 'templates',
                    fields: ['cache_id', 'key', 'type', 'status', 'name', 'width', 'height', 'size', 'readablesize', 'mimetype'],
                    autoLoad: true
                }),
                emptyText: 'No media templates assigned',
                columns: [
                    {
                        dataIndex: 'cache_id',
                        header: this.strings.cache_id,
                        width: 200,
                        hidden: true
                    },
                    {
                        dataIndex: 'key',
                        header: this.strings.key,
                        width: 200
                    },
                    {
                        dataIndex: 'type',
                        header: this.strings.type,
                        renderer: function (v) {
                            return Phlexible.inlineIcon('p-mediatemplates-type_' + v + '-icon') + ' ' + v;
                        },
                        width: 80
                    },
                    {
                        dataIndex: 'status',
                        header: this.strings.status,
                        width: 100
                    },
                    {
                        dataIndex: 'width',
                        header: this.strings.width,
                        width: 70
                    },
                    {
                        dataIndex: 'height',
                        header: this.strings.height,
                        width: 70
                    },
                    {
                        dataIndex: 'size',
                        header: this.strings.size,
                        width: 100,
                        renderer: function (v, md, r) {
                            return v + ' (' + r.data.readablesize + ')';
                        }
                    },
                    {
                        dataIndex: 'mimetype',
                        header: this.strings.mimetype,
                        width: 100
                    }
                ]
            }
        ];

        Phlexible.frontendmedia.MediaTemplatesWindow.superclass.initComponent.call(this);
    }
});
