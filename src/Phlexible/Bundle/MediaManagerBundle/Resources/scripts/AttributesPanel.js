Phlexible.mediamanager.templates.Details = new Ext.XTemplate(
    '<div style="padding: 4px;">',
    '<div><div style="float: left; width: 120px; text-align: right; margin-right: 4px; color: grey;">{[Phlexible.mediamanager.Strings.version]}:</div> {[values.version]}</div>',
    '<div><div style="float: left; width: 120px; text-align: right; margin-right: 4px; color: grey;">{[Phlexible.mediamanager.Strings.type]}:</div> {[values.document_type]}</div>',
    '<div><div style="float: left; width: 120px; text-align: right; margin-right: 4px; color: grey;">{[Phlexible.mediamanager.Strings.size]}:</div> {[Phlexible.Format.size(values.size)]}</div>',
    '<div><div style="float: left; width: 120px; text-align: right; margin-right: 4px; color: grey;">{[Phlexible.mediamanager.Strings.created_by]}:</div> {[values.create_user]}</div>',
    '<div><div style="float: left; width: 120px; text-align: right; margin-right: 4px; color: grey;">{[Phlexible.mediamanager.Strings.create_date]}:</div> {[Phlexible.Format.date(values.create_time)]}</div>',
    '</div>'
);

Phlexible.mediamanager.AttributesPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.mediamanager.Strings.no_file_selected,
    strings: Phlexible.mediamanager.Strings,
//    collapsible: true,
    autoScroll: true,
    /*layout: 'accordion',
     layoutConfig: {
     // layout-specific configs go here
     titleCollapse: true,
     fill: false
     },*/

    folder_rights: {},
    mode: '',

    initComponent: function () {
        this.initAccordionPanels();

        this.items = [
            {
                xtype: 'mediamanager-filepreviewpanel',
                header: false,
                border: false
            },
            {
                header: false,
                border: false,
                autoHeight: true
            },
            {
                xtype: 'panel',
                layout: 'accordion',
                header: false,
                border: false,
                layoutConfig: {
                    titleCollapse: true,
                    fill: true
                },
                items: this.accordionPanels
            }
        ];

        delete this.accordionPanels;

        Phlexible.mediamanager.AttributesPanel.superclass.initComponent.call(this);
    },

    initAccordionPanels: function() {
        this.accordionPanels = [];

        this.fileVersionsIndex = this.accordionPanels.length;
        this.accordionPanels.push({
            xtype: 'fileversionspanel',
            border: false,
            autoHeight: true,
            collapsed: true,
            listeners: {
                render: function (c) {
                    this.relayEvents(c, ['versionSelect', 'versionDownload']);
                },
//                versionChange: function() {
//
//                },
                scope: this
            }
        });

        this.fileAttributesIndex = this.accordionPanels.length;
        this.accordionPanels.push({
            xtype: 'propertygrid',
            iconCls: 'p-mediamanager-properties-icon',
            title: this.strings.attributes,
            viewConfig: {
                emptyText: this.strings.no_attribute_values,
                forceFit: true
            },
            source: {},
            border: false,
            autoHeight: true,
            collapsed: true
        });

        this.folderMetaIndex = this.accordionPanels.length;
        this.accordionPanels.push({
            xtype: 'mediamanager-foldermeta',
            border: false,
            autoHeight: true,
            collapsed: true,
            small: true
        });

        this.fileMetaIndex = this.accordionPanels.length;
        this.accordionPanels.push({
            xtype: 'mediamanager-filemeta',
            border: false,
            autoHeight: true,
            collapsed: true,
            small: true
        });

        if (Phlexible.User.isGranted('ROLE_SUPER_ADMIN')) {
            this.debugFileIndex = this.accordionPanels.length;
            this.accordionPanels.push({
                xtype: 'editorgrid',
                title: 'Debug File',
                iconCls: 'p-gui-menu_debug-icon',
                border: false,
                stripeRows: true,
                autoHeight: true,
                autoExpandColumn: 'value',
                collapsed: true,
                store: new Ext.data.SimpleStore({
                    fields: ['key', 'value']
                }),
                columns: [
                    {
                        header: 'key',
                        dataIndex: 'key'
                    },
                    {
                        id: 'value',
                        header: 'value',
                        dataIndex: 'value',
                        editor: new Ext.form.TextField()
                    }
                ]
            });

            this.debugCacheIndex = this.accordionPanels.length;
            this.accordionPanels.push({
                xtype: 'editorgrid',
                title: 'Debug Cache',
                iconCls: 'p-gui-menu_debug-icon',
                border: false,
                stripeRows: true,
                autoHeight: true,
                autoExpandColumn: 'link',
                collapsed: true,
                store: new Ext.data.SimpleStore({
                    fields: ['key', 'status', 'link']
                }),
                columns: [
                    {
                        header: 'key',
                        dataIndex: 'key'
                    },
                    {
                        header: 'status',
                        dataIndex: 'status',
                        width: 50
                    },
                    {
                        id: 'link',
                        header: 'link',
                        dataIndex: 'link'
                    }
                ],
                sm: new Ext.grid.RowSelectionModel({
                    singleSelect: true
                }),
                listeners: {
                    rowdblclick: function (grid, rowIndex) {
                        var r = grid.store.getAt(rowIndex);

                        window.open(r.data.link);
                    }
                }
            });
        }

        this.usedFolderIndex = this.accordionPanels.length;
        this.accordionPanels.push({
            xtype: 'editorgrid',
            title: 'Folder used by',
            iconCls: 'p-mediamanager-used_by-icon',
            border: false,
            stripeRows: true,
            autoHeight: true,
            //autoExpandColumn: 'value',
            hidden: true,
            collapsed: true,
            store: new Ext.data.ObjectStore({
                fields: ['usage_type', 'usage_id', 'status', 'link']
            }),
            columns: [
                {
                    header: 'usage_type',
                    dataIndex: 'usage_type'
                },
                {
                    header: 'usage_id',
                    dataIndex: 'usage_id'
                },
                {
                    header: 'status',
                    dataIndex: 'status',
                    renderer: function (v) {
                        var out = '';
                        if (v & 8) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_green.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        if (v & 4) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_yellow.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        if (v & 2) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_gray.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        if (v & 1) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_black.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        return out;
                    }
                }
            ]
        });

        this.usedFileIndex = this.accordionPanels.length;
        this.accordionPanels.push({
            xtype: 'editorgrid',
            title: 'File used by',
            iconCls: 'p-mediamanager-used_by-icon',
            border: false,
            stripeRows: true,
            autoHeight: true,
            //autoExpandColumn: 'value',
            hidden: true,
            collapsed: true,
            store: new Ext.data.ObjectStore({
                fields: ['usage_type', 'usage_id', 'status', 'link']
            }),
            columns: [
                {
                    header: 'usage_type',
                    dataIndex: 'usage_type'
                },
                {
                    header: 'usage_id',
                    dataIndex: 'usage_id'
                },
                {
                    header: 'status',
                    dataIndex: 'status',
                    renderer: function (v) {
                        var out = '';
                        if (v & 8) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_green.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        if (v & 4) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_yellow.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        if (v & 2) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_gray.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        if (v & 1) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_black.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        return out;
                    }
                }
            ]
        });
    },

    getPreviewPanel: function () {
        return this.getComponent(0);
    },

    getDetailsPanel: function () {
        return this.getComponent(1);
    },

    getAccordionPanel: function () {
        return this.getComponent(2);
    },

    getFileVersionsPanel: function () {
        return this.getAccordionPanel().getComponent(this.fileVersionsIndex);
    },

    getFileAttributesPanel: function () {
        return this.getAccordionPanel().getComponent(this.fileAttributesIndex);
    },

    getFolderMetaPanel: function () {
        return this.getAccordionPanel().getComponent(this.folderMetaIndex);
    },

    getFileMetaPanel: function () {
        return this.getAccordionPanel().getComponent(this.fileMetaIndex);
    },

    getFolderUsedPanel: function () {
        return this.getAccordionPanel().getComponent(this.usedFileIndex);
    },

    getFileUsedPanel: function () {
        return this.getAccordionPanel().getComponent(this.usedFolderIndex);
    },

    getFileDebugPanel: function () {
        return this.getAccordionPanel().getComponent(this.debugFileIndex);
    },

    getCacheDebugPanel: function () {
        return this.getAccordionPanel().getComponent(this.debugCacheIndex);
    },

    setFolderRights: function (folder_rights) {
        this.folder_rights = folder_rights;

        this.getFileMetaPanel().setRights(folder_rights);
        this.getFolderMetaPanel().setRights(folder_rights);
    },

    setFolderUsage: function (used_in) {
        // folder usage
        this.getFolderUsedPanel().store.loadData(used_in);
        this.getFolderUsedPanel().setTitle('Folder used by [' + this.getFolderUsedPanel().store.getCount() + ']');
        if (this.getFolderUsedPanel().store.getCount()) {
            this.getFolderUsedPanel().show();
        }
        else {
            this.getFolderUsedPanel().hide();
        }
    },

    loadFolderMeta: function (folder_id) {
        this.getFolderMetaPanel().loadMeta({folder_id: folder_id});
    },

    load: function (r) {
        this.setTitle(r.get('name').shorten(40));

        this.getPreviewPanel().loadRecord(r);

        var properties = r.get('properties');
        this.file_id = r.get('id');
        this.file_version = r.get('version');

//        this.attributesPanel.setTitle(this.strings.attributes + ' [' + properties.attributesCnt + ']');
//        this.attributesPanel.setSource(properties.attributes);
        var details = {
            document_type: r.get('document_type'),
            version: r.get('version'),
            size: r.get('size'),
            create_time: r.get('create_time'),
            create_user: r.get('create_user')
        };

        Phlexible.mediamanager.templates.Details.overwrite(this.getDetailsPanel().body, details);

//        this.doLayout();

        if (properties.versions) {
            this.getFileVersionsPanel().loadFile(this.file_id);
        }
        else {
            this.getFileVersionsPanel().empty();
        }

        this.getFileAttributesPanel().setSource(r.get('attributes'));

        this.getFileMetaPanel().loadMeta({
            file_id: this.file_id,
            file_version: this.file_version
        });

        if (Phlexible.User.isGranted('ROLE_SUPER_ADMIN')) {
            var debugData = [
                ['fileId', this.file_id],
                ['fileVersion', this.file_version],
                ['fileSize', r.get('size')],
                ['folderId', r.get('folder_id')],
                ['folder', r.get('folder')],
                ['mimeType', r.get('mime_type')],
                ['documentTypeKey', r.get('document_type_key')],
                ['documentType', r.get('document_type')],
                ['assetType', r.get('asset_type')]
            ];
            this.getFileDebugPanel().store.loadData(debugData);

            var cacheData = [];
            for (var i in r.data.cache) {
                cacheData.push([
                    i,
                    r.data.cache[i],
                    r.data.cache[i]
                ]);
            }
            this.getCacheDebugPanel().store.loadData(cacheData);
        }

        // file usage
        this.getFileUsedPanel().store.loadData(r.data.used_in);
        this.getFileUsedPanel().setTitle('File used by [' + this.getFileUsedPanel().getStore().getCount() + ']');
        if (this.getFileUsedPanel().store.getCount()) {
            this.getFileUsedPanel().show();
        }
        else {
            this.getFileUsedPanel().hide();
        }
    },

    empty: function () {
        this.setTitle(this.strings.no_file_selected);

        // preview
        this.getPreviewPanel().empty();

        // info
        this.getDetailsPanel().body.update('');

        // file attributes
        this.getFileAttributesPanel().setSource({});

        // folder meta
        this.getFolderMetaPanel().empty();

        // file meta
        this.getFileMetaPanel().empty();

        // debug
        if (Phlexible.User.isGranted('ROLE_SUPER_ADMIN')) {
            this.getFileDebugPanel().getStore().removeAll();
            this.getCacheDebugPanel().getStore().removeAll();
        }

        // file usage
        this.getFileUsedPanel().getStore().removeAll();

        // folder usage
        this.getFolderUsedPanel().getStore().removeAll();
    }
});

Ext.reg('mediamanager-attributespanel', Phlexible.mediamanager.AttributesPanel);