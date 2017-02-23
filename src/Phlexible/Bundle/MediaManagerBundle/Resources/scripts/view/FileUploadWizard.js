Ext.provide('Phlexible.mediamanager.FileUploadWizard');

Ext.require('Phlexible.mediamanager.Meta');
Ext.require('Phlexible.mediamanager.MetaGrid');

Phlexible.FileUploadWizardMeta = Ext.extend(Phlexible.mediamanager.Meta, {
    getRight: function() {
        return Phlexible.mediamanager.Rights.FILE_MODIFY;
    },

    initUrls: function () {
        this.urls = {
            load: function(params, meta) {
                debugger;
                Ext.Ajax.request({
                    url: Phlexible.Router.generate('mediamanager_upload_metaset'),
                    params: {
                        set_id: params.set_id
                    },
                    success: function (response) {
                        var data = Ext.decode(response.responseText);

                        this.add(this.createMetaGridConfig(data.metaset.set_id, data.metaset.title, data.metaset.fields, false));

                        this.doLayout();
                    },
                    scope: this
                });
            },
            save: function() {

            }
        };

        this.metasetUrls = {
            list: function() {
                return [];
            },
            save: function(values) {
                console.log(values);
            },
            available: Phlexible.Router.generate('metasets_sets_list')
        };
    },

    createMetaGridConfig: function(setId, title, fields, small) {
        return {
            xtype: 'mediamanager-fileuploadwizard-metagrid',
            setId: setId,
            title: title,
            height: 180,
            border: false,
            small: small,
            data: fields
        };
    }
});
Ext.reg('mediamanager-fileuploadwizard-meta', Phlexible.FileUploadWizardMeta);

Phlexible.FileUploadWizardMetaGrid = Ext.extend(Phlexible.mediamanager.MetaGrid, {
    title: 'Meta yea!'
});
Ext.reg('mediamanager-fileuploadwizard-metagrid', Phlexible.FileUploadWizardMetaGrid);

Phlexible.mediamanager.FileUploadWizardTemplate = new Ext.XTemplate(
    '<tpl for=".">',
        '<div class="p-filereplace-wrap">',
            '<div style="padding-left: 20px;">',
                '<div>',
                    '<div class="p-filereplace-img">',
                        '<img src="{src}" width="48" height="48">',
                    '</div>',
                    '<div class="p-filereplace-desc">',
                        '<div class="p-filereplace-name" style="font-weight: bold">{[values.name.shorten(50)]}</div>',
                        '<div class="p-filereplace-type">{[Phlexible.documenttypes.DocumentTypes.getText(values.type)]}</div>',
                        '<div class="p-filereplace-size">' + Phlexible.mediamanager.Strings.size + ': {[Phlexible.Format.size(values.size)]}</div>',
                    '</div>',
                '</div>',
            '</div>',
        '</div>',
    '</tpl>'
);

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

    meta: [],

    initComponent: function () {
        this.metaStore = new Ext.data.SimpleStore({
            fields: ['set_id', 'key', 'tkey', 'value_de', 'value_en', 'type', 'required', 'synchronized', 'options']
        });

        this.items = [
            {
                xtype: 'dataview',
                region: 'north',
                height: 100,
                itemSelector: 'div.p-fileuploadwizard-wrap',
                overClass: 'p-fileuploadwizard-wrap-over',
                style: 'overflow:auto',
                singleSelect: true,
                store: new Ext.data.JsonStore({
                    fields: ['action', 'header', 'text', 'id', 'name', 'type', 'size', 'src']
                }),
                tpl: Phlexible.mediamanager.FileUploadWizardTemplate
            },
            {
                title: false,
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
                                    url: '',//Phlexible.Router.generate('mediamanager_upload_metasets'),
                                    fields: ['key', 'title'],
                                    root: 'metasets',
                                    autoLoad: false,
                                    listeners: {
                                        load: function () {
                                            debugger;
                                            if (this.currentRecord) {
                                                this.getFileForm().getForm().setValues({
                                                    metaset: this.currentRecord.data.parsed.metaset
                                                });
                                            }
                                        },
                                        scope: this
                                    }
                                }),
                                displayField: 'title',
                                valueField: 'key',
                                mode: 'remote',
                                lazyInit: false,
                                triggerAction: 'all',
                                listeners: {
                                    select: function (combo) {
                                        this.buildMeta(combo.getValue());
                                    },
                                    clear: function () {
                                        this.buildMeta(null);
                                    },
                                    scope: this
                                }
                            }
                        ]
                    },
                    {
                        xtype: 'mediamanager-fileuploadwizard-meta',
                        region: 'center'
                    }/*,
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
                                editorselect: function (editor, record) {
                                    if (record.data.type == 'select') {
                                        editor.field.store.loadData(record.data.options);
                                    }
                                },
                                scope: this
                            }
                        }),
                        sm: new Ext.grid.CellSelectionModel(),
                        listeners: {
                            beforeedit: function (e) {
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
                            afteredit: this.validateMeta,
                            scope: this
                        }
                    }*/
                ]
            }
        ];

        this.bbar = [
            {
                text: 'Discard all remaining files',
                iconCls: 'p-mediamanager-wizard_discard-icon',
                all: true,
                handler: function() {
                    this.uploadChecker.discardAllFiles({}, this.close, this);
                },
                scope: this
            },
            '->',
            {
                text: 'Discard file',
                iconCls: 'p-mediamanager-wizard_discard-icon',
                'do': 'replace',
                handler: function() {
                    this.uploadChecker.discardFile({}, this.next, this);
                },
                scope: this
            },
            '-',
            {
                text: 'Keep both files',
                iconCls: 'p-mediamanager-wizard_save-icon',
                'do': 'keep',
                handler: function() {
                    this.uploadChecker.keepFile(this.getValues(), this.next, this);
                },
                scope: this
            },
            {
                text: 'Replace file',
                iconCls: 'p-mediamanager-wizard_save-icon',
                'do': 'replace',
                handler: function() {
                    this.uploadChecker.replaceFile(this.getValues(), this.next, this);
                },
                scope: this
            },
            {
                text: 'Save as new version',
                iconCls: 'p-mediamanager-wizard_save-icon',
                'do': 'version',
                handler: function() {
                    this.uploadChecker.saveFileVersion(this.getValues(), this.next, this);
                },
                scope: this
            },
            {
                text: 'Save file',
                iconCls: 'p-mediamanager-wizard_save-icon',
                'do': 'save',
                handler: function() {
                    this.uploadChecker.saveFile(this.getValues(), this.close, this);
                },
                scope: this
            }
        ];

        Phlexible.mediamanager.FileUploadWizard.superclass.initComponent.call(this);
    },

    getFileView: function() {
        return this.getComponent(0);
    },

    getFileWrap: function() {
        return this.getComponent(1);
    },

    getFileForm: function() {
        return this.getFileWrap().getComponent(0);
    },

    getMetaGrid: function() {
        return this.getFileWrap().getComponent(1);
    },

    getConflictPanel: function() {
        return this.getFileForm().getComponent(0);
    },

    getAltNameField: function() {
        return this.getFileForm().getComponent(1);
    },

    loadFile: function () {
        this.next();
    },

    buildMeta: function (metaset) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_upload_meta'),
            params: {
                metaset: metaset
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                var grid = this.getMetaGrid();

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
                    else if (this.currentRecord.get('parsed')[key]) {
                        storeData.push([
                            data[i].set_id,
                            key,
                            data[i].tkey,
                            this.currentRecord.get('parsed')[key],
                            (1 == data[i]['synchronized']) ? this.currentRecord.get('parsed')[key] : '',
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
        var file = this.uploadChecker.getCurrentFile(),
            data = [];

        if (!file) {
            this.close();
            return;
        }

        if (!file.get('versions')) {
            data.push({
                id: file.get('new_id'),
                name: file.get('old_name'),
                type: file.get('new_type'),
                size: file.get('new_size'),
                src: Phlexible.Router.generate('mediamanager_upload_preview', {key: file.get('temp_key'), id: file.get('temp_id'), template: '_mm_medium'})
            });
        } else {
            data.push({
                id: file.get('new_id'),
                name: file.get('new_name'),
                type: file.get('new_type'),
                size: file.get('new_size'),
                src: Phlexible.Router.generate('mediamanager_upload_preview', {key: file.get('temp_key'), id: file.get('temp_id'), template: '_mm_medium'})
            });
        }

        this.getFileView().getStore().loadData(data);

        this.meta = {};
        if (file.get('parsed')) {
            this.meta = file.get('parsed');
        }

        var bbar = this.getBottomToolbar();
        if (file.get('old_id')) {
            this.getConflictPanel().show();
            this.getAltNameField().show();
            bbar.items.items[4].show();
            if (file.get('versions')) {
                bbar.items.items[5].hide();
                bbar.items.items[6].show();
            }
            else {
                bbar.items.items[5].show();
                bbar.items.items[6].hide();
            }
            bbar.items.items[7].hide();
        } else {
            this.getConflictPanel().hide();
            this.getAltNameField().hide();
            bbar.items.items[4].hide();
            bbar.items.items[5].hide();
            if (file.get('versions') && file.get('file_id')) {
                bbar.items.items[6].show();
                bbar.items.items[7].hide();
            }
            else {
                bbar.items.items[6].hide();
                bbar.items.items[7].show();
            }
        }

        var form = this.getFileForm(),
            values = {
                altname: file.get('alternative_name') || ''
            };

        if (file.get('parsed') && file.get('parsed').metaset) {
            values.metaset = file.get('parsed').metaset;
        }

        form.getForm().setValues(values);

        if (file.get('parsed') && file.get('parsed').metaset) {
            this.buildMeta(file.get('parsed').metaset);
        }
    },

    validateMeta: function () {
        var valid = true,
            metaRecords = this.getMetaGrid().getStore().getRange(),
            row;

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

    getValues: function () {
        var values = this.getFileForm().getForm().getValues(),
            metaRecords = this.getMetaGrid().getStore().getRange(),
            metaValues = {},
            metaKey, metaData, i;

        for (i = 0; i < metaRecords.length; i++) {
            metaKey = metaRecords[i].data.key;
            metaData = metaRecords[i].data;

            if (1 == metaData['synchronized']) {
                metaData.value_en = metaData.value_de;
            }

            if (1 == metaData.required) {
                if (!this.isRequiredFieldFilled(metaData)) {
                    return;
                }
            }

            if (metaData.type == 'date') {
                metaData.value_de = this.formatDate(metaData.value_de);
                metaData.value_en = this.formatDate(metaData.value_en);
            }

            metaValues[metaKey] = {
                set_id: metaData.set_id,
                value_de: metaData.value_de,
                value_en: metaData.value_en
            };
        }

        values.meta = Ext.encode(metaValues);

        return values;
    },

    formatDate: function (v) {
        var dt;

        if (typeof v !== 'object') {
            dt = Date.parseDate(v, 'Y-m-d');
        } else {
            dt = v;
        }

        if (dt) {
            v = dt.format('d.m.Y');
        }

        return v;
    },

    formatField: function (v, md, r, ri, ci, s) {
        //Phlexible.console.debug(v, md, r, ri, ci, s);

        var type = r.data.type,
            isSynchronized = (1 == r.data['synchronized']);

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
        var languages = Phlexible.Config.get('set.language.frontend'),
            code, field;

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
