Ext.namespace('Phlexible.mediamanager.portlet');

Phlexible.mediamanager.portlet.LatestFilesRecord = Ext.data.Record.create([
    {name: 'id', type: 'string'},
    {name: 'file_id', type: 'string'},
    {name: 'file_version', type: 'int'},
    {name: 'folder_id', type: 'string'},
    {name: 'folder_path', type: 'string'},
    {name: 'document_type_key', type: 'string'},
    {name: 'time', type: 'date'},
    {name: 'title', type: 'string'},
    {name: 'cache'}
]);

Phlexible.mediamanager.portlet.LatestFilesBigTemplate = new Ext.XTemplate(
    '<tpl for=".">',
    '<div class="thumb-wrap" id="media_last_{id}">',
    '<div class="thumb"><img src="{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.file_id, template_key: \"_mm_large\", file_version: values.file_version})]}<tpl if="!cache._mm_large">?waiting</tpl><tpl if="cache._mm_large">?{[values.cache._mm_large]}</tpl>" width="96" height="96" title="{title}" /></div>',
    '<span>{[values.title.shorten(20)]}</span>',
    '<span class="thumb-date">{time:date("Y-m-d H:i:s")}</span>',
    '</div>',
    '</tpl>',
    '<div class="x-clear"></div>'
);
Phlexible.mediamanager.portlet.LatestFilesSmallTemplate = new Ext.XTemplate(
    '<tpl for=".">',
    '<div class="thumb-wrap" id="media_last_{id}">',
    '<div class="thumb"><img src="{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.file_id, template_key: \"_mm_small\", file_version: values.file_version})]}<tpl if="!cache._mm_small">?waiting</tpl><tpl if="cache._mm_small">?{[values.cache._mm_small]}</tpl>" width="32" height="32" /></div>',
    '<div class="text">',
    '<span>{[values.title.shorten(20)]}</span>',
    '<span class="thumb-type">{[Phlexible.documenttypes.DocumentTypes.getText(values.document_type_key)]}</span>',
    '<span class="thumb-date">{time:date("Y-m-d H:i:s")}</span>',
    '</div>',
    '<div class="x-clear"></div>',
    '</div>',
    '</tpl>',
    '<div class="x-clear"></div>'
);
Phlexible.mediamanager.portlet.LatestFilesListTemplate = new Ext.XTemplate(
    '<tpl for=".">',
    '<div class="thumb-wrap" id="media_last_{id}">',
    '<div class="thumb {[Phlexible.mediamanager.DocumentTypes.getClass(values.document_type_key)]}-small"></div>',
    '<div class="text">',
    '<span>{[values.title.shorten(60)]}</span>',
    '<br />',
    '<span class="thumb-date">{time:date("Y-m-d H:i:s")}, {[Phlexible.mediamanager.DocumentTypes.getText(values.document_type_key)]}</span>',
    '</div>',
    '<div class="x-clear"></div>',
    '</div>',
    '</tpl>'
);

Phlexible.mediamanager.portlet.LatestFiles = Ext.extend(Ext.ux.Portlet, {
    title: Phlexible.mediamanager.Strings.latest_files,
    strings: Phlexible.mediamanager.Strings,
    iconCls: 'p-mediamanager-portlet-icon',
    bodyStyle: 'padding: 5px 5px 5px 5px',

    type: 'small',

    initComponent: function () {
        if (this.record.data.settings.style) {
            this.type = this.record.data.settings.style;
        }

        switch (this.type) {
            case 'big':
                this.extraCls = 'mediamanager-portlet mediamanager-portlet-big';
                var tpl = Phlexible.mediamanager.portlet.LatestFilesBigTemplate;
                break;

            case 'list':
                this.extraCls = 'mediamanager-portlet mediamanager-portlet-list';
                var tpl = Phlexible.mediamanager.portlet.LatestFilesListTemplate;
                break;

            default:
                this.extraCls = 'mediamanager-portlet mediamanager-portlet-small';
                var tpl = Phlexible.mediamanager.portlet.LatestFilesSmallTemplate;
                break;
        }
        this.store = new Ext.data.SimpleStore({
            fields: Phlexible.mediamanager.portlet.LatestFilesRecord,
            id: 'id',
            sortInfo: {field: 'time', direction: 'DESC'}
        });

        var data = this.record.get('data');
        if (data) {
            Ext.each(data, function (item) {
                item.time = new Date(item.time * 1000);
                this.add(new Phlexible.mediamanager.portlet.LatestFilesRecord(item, item.id));
            }, this.store);
        }

        this.items = [
            {
                xtype: 'dataview',
                itemSelector: 'div.thumb-wrap',
                overClass: 'thumb-wrap-over',
                style: 'overflow: auto',
                singleSelect: true,
                emptyText: this.strings.no_recent_files,
                deferEmptyText: false,
                autoHeight: true,
                store: this.store,
                tpl: tpl,
                listeners: {
                    click: {
                        fn: function (c, index, node) {
                            var r = this.store.getAt(index);

                            Phlexible.Frame.loadPanel(
                                'Media_manager_MediamanagerPanel',
                                Phlexible.mediamanager.MediamanagerPanel,
                                {
                                    start_file_id: r.get('file_id'),
                                    start_folder_path: r.get('folder_path')
                                }
                            );
                        },
                        scope: this
                    }
                }
            }
        ];

        Phlexible.mediamanager.portlet.LatestFiles.superclass.initComponent.call(this);
    },

    updateData: function (data) {
        var latestFilesMap = [];

        for (var i = 0; i < data.length; i++) {
            var row = data[i];
            latestFilesMap.push(row.id);
            var r = this.store.getById(row.id);
            if (!r) {
                row.time = new Date(row.time * 1000);
                this.store.insert(0, new Phlexible.mediamanager.portlet.LatestFilesRecord(row, row.id));

                Ext.fly('media_last_' + row.id).frame('#8db2e3', 1);
            }
            else {
                var needUpdate = false;
                for (var j in row.cache) {
                    if (row.cache[j] != r.data.cache[j]) {
                        needUpdate = true;
                        break;
                    }
                }
                if (needUpdate) {
                    r.beginEdit();
                    r.set('cache', null);
                    r.set('cache', row.cache);
                    r.endEdit();
                }
            }
        }

        for (var i = this.store.getCount() - 1; i >= 0; i--) {
            var r = this.store.getAt(i);
            if (latestFilesMap.indexOf(r.id) == -1) {
                this.store.remove(r);
            }
        }

        this.store.sort('time', 'DESC');
        this.getComponent(0).refresh();
    }
});

Ext.reg('mediamanager-portlet-latestfiles', Phlexible.mediamanager.portlet.LatestFiles);