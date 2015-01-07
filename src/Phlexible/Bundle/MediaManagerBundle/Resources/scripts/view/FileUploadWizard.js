Ext.provide('Phlexible.mediamanager.FileUploadWizard');

Phlexible.mediamanager.FileUploadWizard = Ext.extend(Ext.Window, {
    title: 'Wizard',
    iconCls: 'p-mediamanager-wizard-icon',
    width: 800,
    minWidth: 800,
    height: 500,
    minHeight: 500,
    layout: 'border',
    modal: true,
    closable: false,

    files: [],
    meta: [],

    initComponent: function () {
        var files = [];
        for (var i = 0; i < this.files.length; i++) {
            files.push([
                this.files[i].temp_key,
                this.files[i].temp_id,
                this.files[i].old_id,
                (this.files[i].old_id ? true : false),
                this.files[i].new_name,
                this.files[i].new_size,
                this.files[i].new_type,
                this.files[i].alternative_name,
                this.files[i].parsed,
                this.files[i].versions || 0,
                this.files[i].file_id || null
            ]);
        }

        this.filesStore = new Ext.data.SimpleStore({
            fields: ['temp_key', 'temp_id', 'old_id', 'conflict', 'name', 'size', 'type', 'altname', 'parsed', 'versions', 'file_id'],
            data: files
        });

        this.metaStore = new Ext.data.SimpleStore({
            fields: ['set_id', 'key', 'tkey', 'value_de', 'value_en', 'type', 'required', 'synchronized', 'options']
        });

        this.items = [
            {
                xtype: 'grid',
                title: 'Uploaded files',
                region: 'north',
                height: 120,
                collapsible: true,
                store: this.filesStore,
                autoExpandColumn: 1,
                columns: [
                    {
                        header: '&nbsp;',
                        width: 30,
                        renderer: function (v, md, r, rowIndex) {
                            if (rowIndex === 0) {
                                v = Phlexible.inlineIcon('p-mediamanager-wizard_current-icon');
                            } else {
                                v = '';
                            }

                            return v;
                        }
                    },
                    {
                        header: 'Name',
                        dataIndex: 'name',
                        renderer: function (v) {
                            return v.shorten(80);
                        }
                    },
                    {
                        header: '&nbsp;',
                        width: 30,
                        dataIndex: 'conflict',
                        renderer: function (v, md, r) {
                            if (v) {
                                v = Phlexible.inlineIcon('p-mediamanager-wizard_conflict-icon');
                            } else {
                                v = '';
                            }

                            return v;
                        }
                    },
                    {
                        header: 'Type',
                        dataIndex: 'type',
                        width: 120,
                        renderer: function (v) {
                            return Phlexible.documenttypes.DocumentTypes.getText(v);
                        }
                    },
                    {
                        header: 'Size',
                        dataIndex: 'size',
                        width: 60,
                        renderer: function (v) {
                            return Phlexible.Format.size(v);
                        }
                    }
                ]
            },
            {
                title: 'Current file',
                region: 'center',
                layout: 'border',
                items: [
                    {
                        xtype: 'form',
                        region: 'north',
                        height: 80,
                        bodyStyle: 'padding: 5px',
                        labelWidth: 80,
                        border: false,
                        items: [
                            {
                                border: false,
                                html: Phlexible.inlineIcon('p-mediamanager-wizard_conflict-icon') + ' <b>A file with this name already exists. Selecting "Keep both files" will save it as:</b>'
                            },
                            {
                                xtype: 'displayfield',
                                hideLabel: true,
                                name: 'altname',
                                anchor: '-10',
                                allowBlank: false,
                                style: 'padding-bottom: 10px;'
                            },
                            {
                                xtype: 'twincombobox',
                                fieldLabel: 'MetaSet',
                                hiddenName: 'metaset',
                                editable: false,
                                anchor: '-10',
                                store: new Ext.data.JsonStore({
                                    url: Phlexible.Router.generate('mediamanager_upload_metasets'),
                                    fields: ['key', 'title'],
                                    root: 'metasets',
                                    autoLoad: true,
                                    listeners: {
                                        load: {
                                            fn: function () {
                                                if (this.currentRecord) {
                                                    this.getComponent(1).getComponent(0).getForm().setValues({
                                                        metaset: this.currentRecord.data.parsed.metaset
                                                    });
                                                }
                                            },
                                            scope: this
                                        }
                                    }
                                }),
                                displayField: 'title',
                                valueField: 'key',
                                mode: 'remote',
                                lazyInit: false,
                                triggerAction: 'all',
                                listeners: {
                                    select: {
                                        fn: function (combo) {
                                            this.buildMeta(combo.getValue());
                                        },
                                        scope: this
                                    },
                                    clear: {
                                        fn: function () {
                                            this.buildMeta(null);
                                        },
                                        scope: this
                                    }
                                }
                            }
                        ]
                    },
                    {
                        xtype: 'editorgrid',
                        cls: 'p-mediamanager-wizard-meta',
                        region: 'center',
                        autoExpandColumn: 2,
                        stripeRows: true,
                        store: this.metaStore,
                        cm: new Phlexible.gui.grid.TypeColumnModel({
                            columns: [
                                {
                                    header: 'Key',
                                    dataIndex: 'tkey',
                                    width: 100
                                },
                                {
                                    header: '&nbsp;',
                                    dataIndex: 'required',
                                    width: 30,
                                    renderer: function (v) {
                                        return 1 == v
                                            ? Phlexible.inlineIcon('p-mediamanager-wizard_required-icon')
                                            : '';
                                    }
                                },
                                {
                                    header: 'Value (de)',
                                    dataIndex: 'value_de',
                                    editable: true,
                                    width: 300,
                                    renderer: this.formatField.createDelegate(this)
                                },
                                {
                                    header: 'Value (en)',
                                    dataIndex: 'value_en',
                                    editable: true,
                                    width: 300,
                                    renderer: this.formatField.createDelegate(this)
                                }
                            ],
                            store: this.metaStore,
                            editors: {
                                textfield: new Ext.form.TextField(),
                                textarea: new Ext.form.TextArea(),
                                date: new Ext.form.DateField({
                                    format: 'd.m.Y'
                                }),
                                'boolean': new Ext.form.ComboBox({
                                    store: new Ext.data.SimpleStore({
                                        fields: ['value'],
                                        data: [
                                            ['true'],
                                            ['false']
                                        ]
                                    }),
                                    displayField: 'value',
                                    mode: 'local',
                                    triggerAction: 'all'
                                }),
                                select: new Ext.form.ComboBox({
                                    store: new Ext.data.SimpleStore({
                                        fields: ['key', 'value']
                                    }),
                                    valueField: 'key',
                                    displayField: 'value',
                                    mode: 'local',
                                    triggerAction: 'all'
                                })
                            },
                            listeners: {
                                editorselect: {
                                    fn: function (editor, record) {
                                        if (record.data.type == 'select') {
                                            editor.field.store.loadData(record.data.options);
                                        }
                                    },
                                    scope: this
                                }
                            }
                        }),
                        sm: new Ext.grid.CellSelectionModel(),
                        listeners: {
                            beforeedit: {
                                fn: function (e) {
                                    var field = e.field;
                                    var record = e.record;
                                    var isSynchronized = (1 == record.get('synchronized'));

                                    // skip editing english values if language is synchronized
                                    if ('value_en' === field && isSynchronized) {
                                        return false;
                                    }

                                    if (e.record.data.type == 'suggest') {
                                        var optionsIndex = 'values_' + field.substring(field.length - 2);

                                        var w = new Phlexible.metasets.MetaSuggestWindow({
                                            record: record,
                                            valueField: field,
                                            optionsField: optionsIndex,
                                            listeners: {
                                                store: {
                                                    fn: this.validateMeta,
                                                    scope: this
                                                }
                                            }
                                        });

                                        w.show();

                                        return false;
                                    }
                                },
                                scope: this
                            },
                            afteredit: {
                                fn: this.validateMeta,
                                scope: this
                            }
                        }
                    }
                ]
            }
        ];

        this.bbar = [
            {
                text: 'Discard all remaining files',
                iconCls: 'p-mediamanager-wizard_discard-icon',
                all: true,
                hidden: this.files.length < 2,
                handler: this.doDiscard,
                scope: this
            },
            '->',
            {
                text: 'Discard file',
                iconCls: 'p-mediamanager-wizard_discard-icon',
                'do': 'replace',
                handler: this.doDiscard,
                scope: this
            },
            '-',
            {
                text: 'Keep both files',
                iconCls: 'p-mediamanager-wizard_save-icon',
                'do': 'keep',
                handler: this.doSave,
                scope: this
            },
            {
                text: 'Replace file',
                iconCls: 'p-mediamanager-wizard_save-icon',
                'do': 'replace',
                handler: this.doSave,
                scope: this
            },
            {
                text: 'Save as new version',
                iconCls: 'p-mediamanager-wizard_save-icon',
                'do': 'version',
                handler: this.doSave,
                scope: this
            },
            {
                text: 'Save file',
                iconCls: 'p-mediamanager-wizard_save-icon',
                'do': 'save',
                handler: this.doSave,
                scope: this
            }
        ];

        Phlexible.mediamanager.FileUploadWizard.superclass.initComponent.call(this);

        this.on('render', this.doFirstSelect, this);
        this.on('show', this.doFirstSelect, this);
        this.getComponent(0).on('render', this.doFirstSelect, this);
        this.getComponent(1).on('render', this.doFirstSelect, this);
    },

    doFirstSelect: function () {
        if (!this.firstDone && this.isVisible() && this.rendered &&
            this.getComponent(0).rendered &&
            this.getComponent(1).rendered) {
            this.firstDone = true;

            this.next();
        }
    },

    buildMeta: function (metaset) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_upload_meta'),
            params: {
                metaset: metaset
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                var grid = this.getComponent(1).getComponent(1);

                var storeData = [];
                for (var i = 0; i < data.length; i++) {
                    var key = data[i].key;

                    if (this.meta[key]) {
                        storeData.push([
                            data[i].set_id,
                            key,
                            data[i].tkey,
                            this.meta[key],
                            (1 == data[i]['synchronized']) ? this.meta[key] : '',
                            data[i].type,
                            data[i].required,
                            data[i]['synchronized'],
                            data[i].options
                        ]);
                    }
                    else if (this.currentRecord.data.parsed[key]) {
                        storeData.push([
                            data[i].set_id,
                            key,
                            data[i].tkey,
                            this.currentRecord.data.parsed[key],
                            (1 == data[i]['synchronized']) ? this.currentRecord.data.parsed[key] : '',
                            data[i].type,
                            data[i].required,
                            data[i]['synchronized'],
                            data[i].options
                        ]);
                    }
                    else {
                        storeData.push([
                            data[i].set_id,
                            key,
                            data[i].tkey,
                            '',
                            '',
                            data[i].type,
                            data[i].required,
                            data[i]['synchronized'],
                            data[i].options
                        ]);
                    }
                }

                grid.store.removeAll();
                grid.store.loadData(storeData);

                this.validateMeta();
            },
            scope: this
        });
    },

    next: function () {
        if (this.currentRecord) {
            this.filesStore.remove(this.currentRecord);

            if (!this.filesStore.getCount()) {
                this.close();
                return;
            }
        }

        this.currentRecord = this.filesStore.getAt(0);

        this.meta = {};
        if (this.currentRecord.data.parsed) {
            this.meta = this.currentRecord.data.parsed;
        }

        var bbar = this.getBottomToolbar();
        if (this.currentRecord.data.old_id) {
            this.getComponent(1).getComponent(0).getComponent(0).show();
            this.getComponent(1).getComponent(0).getComponent(1).show();
            bbar.items.items[4].show();
            if (this.currentRecord.data.versions) {
                bbar.items.items[5].hide();
                bbar.items.items[6].show();
            }
            else {
                bbar.items.items[5].show();
                bbar.items.items[6].hide();
            }
            bbar.items.items[7].hide();
        } else {
            this.getComponent(1).getComponent(0).getComponent(0).hide();
            this.getComponent(1).getComponent(0).getComponent(1).hide();
            bbar.items.items[4].hide();
            bbar.items.items[5].hide();
            if (this.currentRecord.data.versions && this.currentRecord.data.file_id) {
                bbar.items.items[6].show();
                bbar.items.items[7].hide();
            }
            else {
                bbar.items.items[6].hide();
                bbar.items.items[7].show();
            }
        }

        this.getComponent(1).setTitle(this.currentRecord.data.name);

        var form = this.getComponent(1).getComponent(0);
        form.getForm().setValues({
            altname: this.currentRecord.data.altname || '',
            metaset: this.currentRecord.data.parsed.metaset
        });

        this.buildMeta(this.currentRecord.data.parsed.metaset);
    },

    doDiscard: function (btn) {
        var handler;
        if (btn.all) {
            handler = this.close;
        } else {
            handler = this.next;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_upload_save'),
            params: {
                'do': 'discard',
                all: btn.all ? 1 : 0,
                temp_key: this.currentRecord.data.temp_key,
                temp_id: this.currentRecord.data.temp_id
            },
            success: handler,
            scope: this
        });
    },

    validateMeta: function () {
        var valid = true;

        var metaRecords = this.getComponent(1).getComponent(1).store.getRange();
        for (var i = 0; i < metaRecords.length; i++) {
            row = metaRecords[i].data;

            if (1 == row['synchronized']) {
                metaRecords[i].set('value_en', row.value_de);
            }

            if (1 == row.required) {
                valid &= this.isRequiredFieldFilled(row);
            }

            this.meta[row.key] = row.value_de;
        }

        var bbar = this.getBottomToolbar();
        if (valid) {
            bbar.items.items[4].enable();
            bbar.items.items[5].enable();
            bbar.items.items[6].enable();
            bbar.items.items[7].enable();
        } else {
            bbar.items.items[4].disable();
            bbar.items.items[5].disable();
            bbar.items.items[6].disable();
            bbar.items.items[7].disable();
        }

        return valid;
    },

    doSave: function (btn) {
        var formValues = this.getComponent(1).getComponent(0).getForm().getValues();
        var metaRecords = this.getComponent(1).getComponent(1).store.getRange();
        var meta = {};
        var row, key, values;
        for (var i = 0; i < metaRecords.length; i++) {
            key = metaRecords[i].data.key;
            values = metaRecords[i].data;

            if (1 == values['synchronized']) {
                values.value_en = values.value_de;
            }

            if (1 == values.required) {
                if (!this.isRequiredFieldFilled(values)) {
                    return;
                }
            }

            if (values.type == 'date') {
                values.value_de = this.formatDate(values.value_de);
                values.value_en = this.formatDate(values.value_en);
            }

            meta[key] = {
                set_id: values.set_id,
                value_de: values.value_de,
                value_en: values.value_en
            };
        }

        formValues['do'] = btn['do'];
        formValues.temp_key = this.currentRecord.data.temp_key;
        formValues.temp_id = this.currentRecord.data.temp_id;
        formValues.meta = Ext.encode(meta);

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_upload_save'),
            params: formValues,
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    this.fireEvent('update');

                    this.next();
                }
                else {
                    alert("not valid");
                }
            },
            scope: this
        });
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

    formatField: function (v, md, r, ri, ci, s) {

        Phlexible.console.log(v, md, r, ri, ci, s);

        var type = r.data.type;
        var isSynchronized = (1 == r.data['synchronized']);

        // mark synchronized fields
        if (isSynchronized) {
            if (3 == ci) {
                md.attr = 'style="border:1px solid red;"';
            }
            else if (2 == ci) {
                md.attr = 'style="border:1px solid green;"';
            }
        }

        if (v && type == 'date') {
            v = this.formatDate(v);
        }
        else if (v && type === 'select') {
            for (var i = 0; i < r.data.options.length; i++) {
                if (r.data.options[i][0] == v) {
                    v = r.data.options[i][1];
                    break;
                }
            }
        }

        return v;
    },

    isRequiredFieldFilled: function (data) {
        var code, field;
        var languages = Phlexible.Config.get('set.language.frontend');

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
