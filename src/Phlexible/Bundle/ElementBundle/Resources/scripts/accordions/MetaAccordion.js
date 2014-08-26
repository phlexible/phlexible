Phlexible.elements.accordion.Meta = Ext.extend(Ext.grid.EditorGridPanel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.meta,
    cls: 'p-elements-meta-accordion',
    iconCls: 'p-metaset-component-icon',
    border: false,
    autoHeight: true,
    autoScroll: true,
    autoExpandColumn: 1,
    viewConfig: {
        emptyText: 'No meta values defined.'
    },

    key: 'meta',

    initComponent: function () {
        this.store = new Ext.data.JsonStore({
            fields: ['key', 'type', 'options', 'value', 'readonly', 'required', 'synchronized'],
            listeners: {
                load: function () {
                    this.validateMeta();
                },
                scope: this
            }
        });

        this.sm = new Ext.grid.RowSelectionModel();

        var metaFields = new Phlexible.metasets.Fields();

        this.cm = new Phlexible.gui.grid.TypeColumnModel({
            columns: [
                {
                    header: this.strings.key,
                    dataIndex: 'key',
                    renderer: function (v, md, r) {
                        if (r.data.required) {
                            v = '<b>' + v + '</b>';
                        }
                        return v;
                    }
                },
                {
                    header: this.strings.value,
                    dataIndex: 'value',
                    editor: new Ext.form.TextField(),
                    renderer: function (v, md, r) {
                        if (r.data['synchronized']) {
                            if (this.master) {
                                md.css = md.css + ' synchronized-master';
                            } else {
                                md.css = md.css + ' synchronized-slave';
                            }
                        }

                        return v;
                    }.createDelegate(this)
                }
            ],
            store: this.store,
            grid: this,
            editors: metaFields.getEditors(),
            selectEditorCallbacks: metaFields.getSelectEditorCallbacks(),
            beforeEditCallbacks: metaFields.getBeforeEditCallbacks(),
            afterEditCallbacks: metaFields.getAfterEditCallbacks()
        });

        this.on({
            beforeedit: function (e) {
                var field = e.field;
                var record = e.record;
                var isSynchronized = (1 == record.get('synchronized'));

                // skip editing english values if language is synchronized
                if (!this.master && isSynchronized) {
                    return false;
                }
            },
            afteredit: function (e) {
                this.validateMeta();
            },
            scope: this
        });

        Phlexible.elements.accordion.Meta.superclass.initComponent.call(this);
    },

    load: function (data) {
        if (data.properties.et_type != 'full' || !data.meta || !data.meta.fields) {
            this.hide();
            return;
        }

        this.language = data.properties.language;
        this.master = data.properties.master || 0;

        this.setTitle(this.strings.meta + ' [' + data.meta.fields.length + ']');

        this.store.removeAll();
        this.store.loadData(data.meta.fields);

        this.show();
    },

    getData: function () {
        var data = {};
        var records = this.store.getRange();

        for (var i = 0; i < records.length; i++) {
            data[records[i].data.key] = records[i].data['value'];
        }

        return data;
    },

    xupdateSource: function (response) {
        var source = Ext.decode(response.responseText);
        this.setSource(source);
    },

    isValid: function () {
        return this.validateMeta();
    },

    validateMeta: function () {
        var valid = true;

        var metaRecords = this.getStore().getRange();
        for (var i = 0; i < metaRecords.length; i++) {
            row = metaRecords[i].data;

            //if (1 == row['synchronized']) {
            //    metaRecords[i].set('value_en', row.value_de);
            //}

            if (1 == row.required) {
                valid &= !!row['value'];
            }
        }

        if (valid) {
            this.header.child('span').removeClass('error');
        } else {
            this.header.child('span').addClass('error');
        }

        this.metaValid = valid;

        return valid;
    }
});

Ext.reg('elements-metaaccordion', Phlexible.elements.accordion.Meta);
