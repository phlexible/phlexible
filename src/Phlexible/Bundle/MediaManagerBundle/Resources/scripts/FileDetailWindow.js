Phlexible.mediamanager.FileDetailAttributesTemplate = new Ext.XTemplate(
    '<div style="padding: 4px 4px 8px 4px;">',
    '<div>',
    '<div><div style="float: left; width: 120px; text-align: right; margin-right: 4px; color: grey;">{[Phlexible.mediamanager.Strings.name]}:</div> {[values.name.shorten(80)]}</div>',
    '<div><div style="float: left; width: 120px; text-align: right; margin-right: 4px; color: grey;">{[Phlexible.mediamanager.Strings.path]}:</div> {path}</div>',
    '<div><div style="float: left; width: 120px; text-align: right; margin-right: 4px; color: grey;">{[Phlexible.mediamanager.Strings.type]}:</div> {document_type_key}</div>',
    '<div><div style="float: left; width: 120px; text-align: right; margin-right: 4px; color: grey;">{[Phlexible.mediamanager.Strings.size]}:</div> {[Phlexible.Format.size(values.size)]}</div>',
    '<div><div style="float: left; width: 120px; text-align: right; margin-right: 4px; color: grey;">{[Phlexible.mediamanager.Strings.created_by]}:</div> {create_user_id}</div>',
    '<div><div style="float: left; width: 120px; text-align: right; margin-right: 4px; color: grey;">{[Phlexible.mediamanager.Strings.create_date]}:</div> {[Phlexible.Format.date(values.create_time * 1000)]}</div>',
    '</div>',
    '<div style="padding-top: 8px;">',
    '<tpl for="attributes">',
    '<tpl if="value">',
    '<div><div style="float: left; width: 120px; text-align: right; margin-right: 4px; color: grey;">{key}:</div> {value}</div>',
    '</tpl>',
    '</tpl>',
    '</div>',
    '</div>'
);
Phlexible.mediamanager.FileDetailWindow = Ext.extend(Ext.Window, {
    title: 'File Details',
    strings: Phlexible.mediamanager.Strings,
    width: 900,
    height: 600,
    layout: 'border',
    cls: 'p-mediamanager-detail-window',
    bodyStyle: 'padding: 5px',
    modal: true,
    constrainHeader: true,
    maximizable: true,

    file_id: null,
    file_version: null,
    file_name: null,
    document_type_key: null,
    asset_type: null,
    cache: null,
    rights: [],

    initComponent: function () {
        this.populateTabs();

        this.items = [
            {
                xtype: 'mediamanager-filepreviewpanel',
                region: 'west',
                width: 280,
                height: 300,
                border: false,
                file_id: this.file_id,
                file_version: this.file_version,
                file_name: this.file_name,
                document_type_key: this.document_type_key,
                asset_type: this.asset_type,
                cache: this.cache
            },
            {
                region: 'center',
                xtype: 'tabpanel',
                deferredRender: false,
                activeTab: 1,
                items: this.tabs
            }
        ];

        this.bbar = ['->', {
            text: 'Previous file',
            iconCls: 'p-mediamanager-arrow_left-icon',
            hidden: true,
            handler: function () {
                this.file_id = this.prev.file_id;
                this.file_version = this.prev.file_version;
                this.load();
            },
            scope: this
        }, ' ', {
            text: 'Next file',
            iconCls: 'p-mediamanager-arrow_right-icon',
            hidden: true,
            handler: function () {
                this.file_id = this.next.file_id;
                this.file_version = this.next.file_version;
                this.load();
            },
            scope: this
        }];

        Phlexible.mediamanager.FileDetailWindow.superclass.initComponent.call(this);
    },

    populateTabs: function () {
        this.tabs = [
            {
                xtype: 'fileversionspanel',
                region: 'center',
                file_id: this.file_id,
                file_version: this.file_version,
                listeners: {
                    versionSelect: {
                        fn: this.onVersionSelect,
                        scope: this
                    },
                    versionDownload: {
                        fn: function (file_id, file_version) {
                            var href = Phlexible.Router.generate('mediamanager_download', {id: file_id})

                            if (file_version) {
                                href += '/' + file_version;
                            }

                            document.location.href = href;
                        },
                        scope: this
                    }
                }
            },
            {
                title: 'Attributes',
                iconCls: 'p-mediamanager-file_properties-icon',
                hidden: true
            },
            {
                xtype: 'mediamanager-filemeta',
                border: false,
                listeners: {
                    render: function (c) {
                        c.setRights(this.rights);
                    },
                    scope: this
                }
            }/*,{
             title: 'Rights',
             iconCls: 'p-mediamanager-file_rights-icon',
             hidden: true
             },{
             title: 'Preview',
             iconCls: 'p-mediamanager-file_preview-icon',
             hidden: true
             }*/
        ];
    },

    getPreviewPanel: function () {
        return this.getComponent(0);
    },

    getTabPanel: function () {
        return this.getComponent(1);
    },

    getVersionsPanel: function () {
        return this.getTabPanel().getComponent(0);
    },

    getAttributesPanel: function () {
        return this.getTabPanel().getComponent(1);
    },

    getMetaGrid: function () {
        return this.getTabPanel().getComponent(2);
    },

    onVersionSelect: function (file_id, file_version, file_name, folder_id, document_type_key, asset_type) {
        this.getPreviewPanel().load(file_id, file_version, file_name, document_type_key, asset_type);
        this.loadProperties(file_id, file_version);
    },

    loadProperties: function (file_id, file_version) {
        this.getMetaGrid().loadMeta({
            file_id: file_id,
            file_version: file_version
        });

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_file_properties', {id: file_id, version: file_version}),
            success: function (response) {
                var data = Ext.decode(response.responseText);
                this.setTitle(data.name);
                this.getPreviewPanel().load(data.id, data.version, data.name, data.document_type_key);
                var html = Phlexible.mediamanager.FileDetailAttributesTemplate.applyTemplate(data);
                this.getAttributesPanel().body.update(html);

                var bbar = this.getBottomToolbar();
                if (data.prev) {
                    this.prev = data.prev;
                    bbar.items.items[1].show();
                    bbar.items.items[2].show();
                } else {
                    this.prev = null;
                    bbar.items.items[1].hide();
                    bbar.items.items[2].hide();
                }
                if (data.next) {
                    this.next = data.next;
                    bbar.items.items[3].show();
                    bbar.items.items[2].show();
                } else {
                    this.next = null;
                    bbar.items.items[3].hide();
                    bbar.items.items[2].hide();
                }
            },
            scope: this
        });
    },

    load: function () {
        // properties
        this.loadProperties(this.file_id, this.file_version);

        // versions
        this.getVersionsPanel().loadFile(this.file_id, this.file_version);

        if (this.rendered) this.getComponent(1).getComponent(1).setRights(this.rights);
    },

    show: function () {
        this.load();
        Phlexible.mediamanager.FileDetailWindow.superclass.show.call(this);
    }
});
