Ext.provide('Phlexible.mediamanager.Meta');

Ext.require('Phlexible.mediamanager.MetaGrid');

Phlexible.mediamanager.Meta = Ext.extend(Ext.Panel, {
    strings: Phlexible.mediamanager.Strings,
    title: Phlexible.mediamanager.Strings.file_meta,
    cls: 'p-mediamanager-meta',
    iconCls: 'p-metaset-component-icon',

    small: false,
    right: null,
    key: 'key',
    params: {},

    initUrls: function () {
        alert("Implement me!");
    },

    createMetaGridConfig: function(setId, title, fields, small) {
        alert("Implement me!");
    },

    getRight: function() {
        alert("Implement me!");
    },

    initComponent: function () {
        this.initUrls();

        this.items = [];

        this.populateTbar();

        Phlexible.mediamanager.Meta.superclass.initComponent.call(this);
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
                this.changeLanguage(item.language);
            },
            scope: this
        };

        this.tbar = [{
            text: this.strings.save,
            iconCls: 'p-mediamanager-meta_save-icon',
            handler: this.save,
            scope: this
        },
        '->',
        cycleBtn,
        '-',
        {
            text: this.strings.metasets,
            iconCls: 'p-metaset-component-icon',
            handler: function () {
                var w = new Phlexible.metasets.MetaSetsWindow({
                    baseParams: this.params,
                    urls: this.metasetUrls,
                    listeners: {
                        savesets: this.reloadMeta,
                        updatesets: this.loadMeta,
                        scope: this
                    }
                });
                w.show();
            },
            scope: this
        }];
    },

    loadMeta: function (params) {
        if (typeof this.urls.load === 'function') {
            this.urls.load(params, function(data) {
                this.removeAll();
                if (data.meta && data.meta.length) {
                    Ext.each(data.meta, function (meta) {
                        this.add(this.createMetaGridConfig(meta.set_id, meta.title, meta.fields, this.small));
                    }, this);
                } else {
                    this.add({
                        border: false,
                        html: '<div class="x-grid-empty">' + this.strings.no_meta_values + '</div>'
                    });
                }

                this.doLayout();
            }, this);
            return;
        }

        this.params = params;
        Ext.Ajax.request({
            url: this.urls.load,
            params: params,
            success: function (response) {
                var data = Ext.decode(response.responseText);

                this.removeAll();
                if (data.meta && data.meta.length) {
                    Ext.each(data.meta, function (meta) {
                        this.add(this.createMetaGridConfig(meta.set_id, meta.title, meta.fields, this.small));
                    }, this);
                } else {
                    this.add({
                        border: false,
                        html: '<div class="x-grid-empty">' + this.strings.no_meta_values + '</div>'
                    });
                }

                this.doLayout();
            },
            scope: this
        });
    },

    reloadMeta: function() {
        this.loadMeta(this.params);
    },

    validateMeta: function() {
        var valid = true;
        this.items.each(function(p) {
            if (!p.validateMeta()) {
                Ext.MessageBox.alert(this.strings.error, this.strings.fill_required_fields);
                valid = false;
                return false;
            }
        }, this);
        return valid;
    },

    changeLanguage: function(language) {
        this.items.each(function(p) {
            var cm = p.getColumnModel();
            Ext.each(cm.columns, function (column) {
                if (!column.language) {
                    return;
                }

                cm.setHidden(column.id, column.language != language);
                p.getView().layout();
            }, this);
        }, this);
    },

    setRights: function (rights) {
        if (rights.indexOf(this.getRight()) != -1) {
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

    save: function () {
        if (!this.validateMeta()) {
            return;
        }

        var sources = this.getData();
        var params = this.params;
        params.data = Ext.encode(sources);

        if (typeof this.urls.save) {
            this.urls.save(params, this);
            return
        }

        Ext.Ajax.request({
            url: this.urls.save,
            params: params,
            success: function (response) {
                var result = Ext.decode(response.responseText);
                if (result.success === false) {
                    Ext.MessageBox.alert(this.strings.error, result.msg);
                }
                this.reloadMeta();
            },
            scope: this
        });
    },

    getData: function () {
        var sources = {};
        if (this.items) {
            this.items.each(function (p) {
                sources[p.setId] = p.getData();
            });
        }
        return sources;
    }
});
