Phlexible.mediamanager.FileMetaGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    strings: Phlexible.mediamanager.Strings,
    title: Phlexible.mediamanager.Strings.meta,
    cls: 'p-mediamanager-meta-grid',
    //iconCls: 'p-metaset-component-icon',
    stripeRows: true,
    emptyText: Phlexible.metasets.Strings.no_meta_values,
    enableColumnMove: false,
    viewConfig: {
        emptyText: Phlexible.metasets.Strings.no_meta_values,
        deferEmptyText: true,
        forceFit: false
    },

    small: false,
    right: Phlexible.mediamanager.Rights.FILE_MODIFY,
    key: 'key',
    params: {},

    initComponent: function () {
        if (this.small) {
            this.enableColumnHide = false;
            this.viewConfig.forceFit = true;
        }

        var columns = [
            {
                header: this.strings.key,
                dataIndex: 'key',
                width: 100
            },
            {
                header: '&nbsp;',
                dataIndex: 'required',
                width: 30,
                renderer: function (v) {
                    return 1 == v ? Phlexible.inlineIcon('p-mediamanager-wizard_required-icon') : '';
                }
            }
        ];

        var fields = ['key', 'type', 'options', 'required', 'synchronized', 'readonly'];

        Ext.each(Phlexible.Config.get('set.language.meta'), function (language) {
            fields.push('value_' + language[0]);
            columns.push({
                header: this.strings.value + ' ' + Phlexible.inlineIcon(language[2]) + ' ' + language [1],
                dataIndex: 'value_' + language[0],
                language: language[0],
                width: 200,
                editable: true,
                hidden: this.small && language[0] !== Phlexible.Config.get('language.metasets'),
                renderer: this.formatField.createDelegate(this)
            });
        }, this);

        this.store = new Ext.data.JsonStore({
            fields: fields,
            listeners: {
                load: function (store, records) {
                    // if no required fields are present for a file
                    // -> hide the 'required' column
                    var hasRequiredFields = false;
                    for (var i = records.length - 1; i >= 0; --i) {
                        if (1 == records[i].get('required')) {
                            hasRequiredFields = true;
                            break;
                        }
                    }
                    this.getColumnModel().setHidden(1, !hasRequiredFields);

                    this.validateMeta();
                },
                scope: this
            }
        });

        this.sm = new Ext.grid.CellSelectionModel();

        var metaFields = new Phlexible.metasets.Fields();

        this.cm = new Phlexible.gui.grid.TypeColumnModel({
            columns: columns,
            store: this.store,
            grid: this,
            editors: metaFields.getEditors(),
            selectEditorCallbacks: metaFields.getSelectEditorCallbacks(),
            beforeEditCallbacks: metaFields.getBeforeEditCallbacks(),
            afterEditCallbacks: metaFields.getAfterEditCallbacks()
        });

        Phlexible.mediamanager.FileMetaGrid.superclass.initComponent.call(this);

        this.on({
            render: function () {
                if (this.data) {
                    this.setData(this.data);
                    delete this.data;
                }
            },
            beforeedit: function (e) {
                // skip editing english values if language is synchronized
                var record = e.record;
                var ci = e.column;
                var isSynchronized = (1 == record.get('synchronized'));
                var cm = this.getColumnModel();
                var column = cm.getColumnById(cm.getColumnId(ci));
                if (isSynchronized && (!column.language || column.language !== Phlexible.Config.get('language.metasets'))) {
                    return false;
                }
                if (e.record.data.readonly) {
                    return false;
                }
            },
            afteredit: function (e) {
                this.validateMeta();
            },
            scope: this
        });

    },

    setData: function (data) {
        this.store.loadData(data);
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
    },

    formatDate: function (v) {
        if (typeof v !== 'object') {
            var dt = Date.parseDate(v, 'Y-m-d');
        }
        else {
            var dt = v;
        }

        if (dt) {
            v = dt.format('d.m.Y');
        }

        return v;
    },

    formatField: function (v, md, r, ri, ci, store) {

        var isSynchronized = (1 == r.data['synchronized']);

        // mark synchronized fields
        if (isSynchronized) {
            var cm = this.getColumnModel();
            var language = cm.getColumnById(cm.getColumnId(ci)).language;
            if (language) {
                if (language === Phlexible.Config.get('language.metasets')) {
                    md.attr = 'style="border:1px solid green;"';
                }
                else {
                    md.attr = 'style="border:1px solid red;"';
                }
            }
        }

        if (v && r.data.type == 'date') {
            v = this.formatDate(v);
        }
        else if (v && r.data.type === 'select') {
            for (var i = 0; i < r.data.options.length; i++) {
                if (r.data.options[i][0] == v) {
                    v = r.data.options[i][1];
                    break;
                }
            }
        }

        return v;
    },

    validateMeta: function () {
        var valid = true;
        var languages = Phlexible.Config.get('set.language.meta');
        var language;
        var defaultLanguage = Phlexible.Config.get('language.metasets');

        var metaRecords = this.getStore().getRange();

        for (var i = 0; i < metaRecords.length; i++) {
            row = metaRecords[i].data;

            if (1 == row['synchronized']) {
                for (var j = 0; j < languages.length; ++j) {
                    language = languages[j][0];
                    if (language !== defaultLanguage) {
                        metaRecords[i].set('value_' + language, row['value_' + defaultLanguage]);
                    }
                }
            }

            if (1 == row.required) {
                valid &= this.isRequiredFieldFilled(row);
            }
        }

        /*
         var tbar = this.getTopToolbar();
         if (valid) {
         tbar.items.items[0].enable();
         } else {
         tbar.items.items[0].disable();
         }
         */

        return valid;
    },

    isRequiredFieldFilled: function (data) {
        var code, field;
        var languages = Phlexible.Config.get('set.language.meta');

        for (var i = 0; i < languages.length; ++i) {
            code = languages[i][0];
            field = 'value_' + code;

            if (!data[field]) {
                return false;
            }
        }

        return true;
    }

});

Ext.reg('mediamanager-filemetagrid', Phlexible.mediamanager.FileMetaGrid);
