Phlexible.mediamanager.FileMeta = Ext.extend(Ext.Panel, {
    strings: Phlexible.mediamanager.Strings,
    title: Phlexible.mediamanager.Strings.meta,
    cls: 'p-mediamanager-meta-grid',
    iconCls: 'p-metasets-component-icon',

    small: false,
    right: Phlexible.mediamanager.Rights.FILE_MODIFY,
    key: 'key',
    params: {},

    initUrls: function () {
        this.urls = {
            load: Phlexible.Router.generate('mediamanager_file_meta'),
            save: Phlexible.Router.generate('mediamanager_file_meta_save')
        };

        this.metasetUrls = {
            list: Phlexible.Router.generate('mediamanager_file_meta_set_list'),
            available: Phlexible.Router.generate('mediamanager_file_meta_set_available'),
            add: Phlexible.Router.generate('mediamanager_file_meta_set_add'),
            remove: Phlexible.Router.generate('mediamanager_file_meta_set_remove')
        };
    },

    initComponent: function () {

        this.initUrls();

        this.items = [];

        this.populateTbar();

        Phlexible.mediamanager.FileMeta.superclass.initComponent.call(this);
    },

    populateTbar: function () {
        var toggleId = Ext.id();

        var languageBtns = [];
        Ext.each(Phlexible.Config.get('set.language.meta'), function (item) {
            var language = item[0];
            var t9n = item[1];
            var flag = item[2];
            languageBtns.push({
                text: t9n,
                iconCls: flag,
                language: language,
                checked: Phlexible.Config.get('language.metasets') === language
            });
        }, this);

        var cycleBtn = {
            xtype: 'cycle',
            showText: !this.small,
            items: languageBtns,
            hidden: !this.small,
            changeHandler: function (btn, item) {
                var cm = this.getColumnModel();
                Ext.each(cm.columns, function (column) {
                    if (!column.language) {
                        return;
                    }

                    cm.setHidden(column.id, column.language != item.language);
                }, this);
                this.view.layout();
            },
            scope: this
        };

        this.tbar = [
            {
                text: this.strings.save,
                iconCls: 'p-mediamanager-meta_save-icon',
                handler: function () {
                    if (!this.validateMeta()) {
                        Ext.MessageBox.alert(this.strings.error, this.strings.fill_required_fields);
                        return;
                    }

                    var source = this.getData();
                    var params = this.params;
                    params.data = Ext.encode(source);

                    Ext.Ajax.request({
                        url: this.urls.save,
                        params: params,
                        success: function (response) {
                            var result = Ext.decode(response.responseText);
                            if (result.success === false) {
                                Ext.MessageBox.alert(this.strings.error, result.msg);
                            }
                            this.store.reload();
                        },
                        scope: this
                    });
                },
                scope: this
            },
            '->',
            cycleBtn,
            '-',
            {
                text: this.strings.metasets,
                iconCls: 'p-metasets-component-icon',
                handler: function () {
                    var w = new Phlexible.mediamanager.MetaSetsWindow({
                        baseParams: this.params,
                        urls: this.metasetUrls,
                        listeners: {
                            addset: function () {
                                this.store.reload();
                            },
                            removeset: function () {
                                this.store.reload();
                            },
                            scope: this
                        }
                    });
                    w.show();
                },
                scope: this
            }
        ];
    },

    loadMeta: function (params) {
        this.params = params;
        Ext.Ajax.request({
            url: this.urls.load,
            params: params,
            success: function (response) {
                var data = Ext.decode(response.responseText);

                this.removeAll();
                Ext.each(data.meta, function (meta) {
                    this.add({
                        xtype: 'mediamanager-filemetagrid',
                        title: meta.title,
                        height: 180,
                        border: false,
                        small: this.small,
                        data: meta.fields
                    });
                }, this);

                this.doLayout();
            },
            scope: this
        });
    },

    setRights: function (rights) {
        if (rights.indexOf(this.right) != -1) {
            this.getTopToolbar().items.items[0].show();
            this.getTopToolbar().items.items[4].show();
        } else {
            this.getTopToolbar().items.items[0].hide();
            this.getTopToolbar().items.items[4].hide();
        }
    },

    empty: function () {
        this.removeAll();
    },

    getData: function () {
        var data = {};
        var records = this.store.getRange();

        for (var i = 0; i < records.length; i++) {
            var key = records[i].data.key;
            var values = records[i].data;
            if (values.type == 'date') {
                for (var j in values) {
                    if (j.substr(0, 6) === 'value_') {
                        values[j] = this.formatDate(values[j]);
                    }
                }
            }
            data[key] = values;
        }

        return data;
    }
});

Ext.reg('mediamanager-filemeta', Phlexible.mediamanager.FileMeta);
