Ext.provide('Phlexible.mediamanager.FileUploadWizard');

Ext.require('Phlexible.mediamanager.Meta');
Ext.require('Phlexible.mediamanager.MetaGrid');
Ext.require('Phlexible.metasets.model.Metaset');

Phlexible.FileUploadWizardMeta = Ext.extend(Phlexible.mediamanager.Meta, {
    getRight: function() {
        return Phlexible.mediamanager.Rights.FILE_MODIFY;
    },

    initUrls: function () {
        this.urls = {
            load: function(params, callback, scope) {
                callback.call(scope, params);
            },
            save: function() {

            }
        };

        this.metasetUrls = {
            list: function() {
                debugger;
                return [];
            },
            save: function(values) {
                Phlexible.console.log(values);
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
        '<div class="p-filereplace-wrap p-filewizard-wrap">',
            '<div>',
                '<div><b>{source}</b></div>',
                '<div>',
                    '<div class="p-filereplace-img">',
                        '<img src="{src}" width="48" height="48" />',
                    '</div>',
                    '<div class="p-filereplace-desc">',
                        '<div class="p-filereplace-name" style="font-weight: bold">{[values.name.shorten(50)]}</div>',
                        '<div class="p-filereplace-type">{[Phlexible.documenttypes.DocumentTypes.getText(values.type)]}</div>',
                        '<div class="p-filereplace-size">{[Phlexible.Format.size(values.size)]}</div>',
                    '</div>',
                '</div>',
            '</div>',
        '</div>',
    '</tpl>',
    '<div class="x-clear"></div>',
    '<tpl if="values[1] && values[0].hash===values[1].hash">',
        '<div class="p-filewizard-identical">'+Phlexible.inlineIcon('p-mediamanager-wizard_conflict-icon')+' '+Phlexible.mediamanager.Strings.wizard.identical+'</div>',
    '</tpl>'
);

Phlexible.mediamanager.FileUploadWizard = Ext.extend(Ext.Window, {
    title: Phlexible.mediamanager.Strings.add_file,
    strings: Phlexible.mediamanager.Strings,
    iconCls: 'p-mediamanager-file_add-icon',
    width: 800,
    minWidth: 800,
    height: 500,
    minHeight: 500,
    layout: 'border',
    modal: true,
    closable: false,

    meta: [],

    initComponent: function () {
        /*
        this.metaStore = new Ext.data.SimpleStore({
            fields: ['set_id', 'key', 'tkey', 'value_de', 'value_en', 'type', 'required', 'synchronized', 'options']
        });
        */

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
                    fields: ['action', 'header', 'text', 'id', 'name', 'type', 'size', 'src', 'source', 'hash']
                }),
                tpl: Phlexible.mediamanager.FileUploadWizardTemplate
            },
            {
                title: false,
                region: 'center',
                layout: 'border',
                border: false,
                items: [
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
                text: this.strings.wizard.discard_all,
                iconCls: 'p-mediamanager-wizard_discard-icon',
                all: true,
                handler: function() {
                    this.showLoadMmask(this.strings.wizard.discarding_all);
                    this.uploadChecker.discardAllFiles({}, this.close, this);
                },
                scope: this
            },
            '->',
            {
                text: this.strings.wizard.discard,
                iconCls: 'p-mediamanager-wizard_discard-icon',
                'do': 'replace',
                handler: function() {
                    this.showLoadMmask(this.strings.wizard.discarding);
                    this.uploadChecker.discardFile({}, this.uploadChecker.next, this.uploadChecker);
                },
                scope: this
            },
            '-',
            {
                text: this.strings.wizard.keep_both,
                iconCls: 'p-mediamanager-wizard_save-icon',
                'do': 'keep',
                handler: function() {
                    this.showLoadMmask(this.strings.wizard.keeping_both);
                    this.uploadChecker.keepFile(this.getValues(), this.uploadChecker.next, this.uploadChecker);
                },
                scope: this
            },
            {
                text: this.strings.wizard.replace,
                iconCls: 'p-mediamanager-wizard_save-icon',
                'do': 'replace',
                handler: function() {
                    this.showLoadMmask(this.strings.wizard.replacing);
                    this.uploadChecker.replaceFile(this.getValues(), this.uploadChecker.next, this.uploadChecker);
                },
                scope: this
            },
            {
                text: this.strings.wizard.save_version,
                iconCls: 'p-mediamanager-wizard_save-icon',
                'do': 'version',
                handler: function() {
                    this.showLoadMmask(this.strings.wizard.saving_version);
                    this.uploadChecker.saveFileVersion(this.getValues(), this.uploadChecker.next, this.uploadChecker);
                },
                scope: this
            },
            {
                text: this.strings.wizard.save,
                iconCls: 'p-mediamanager-wizard_save-icon',
                'do': 'save',
                handler: function() {
                    this.showLoadMmask(this.strings.wizard.saving);
                    this.uploadChecker.saveFile(this.getValues(), this.uploadChecker.next, this.uploadChecker);
                },
                scope: this
            }
        ];

        Phlexible.mediamanager.FileUploadWizard.superclass.initComponent.call(this);
    },

    showLoadMmask: function(msg) {
        if (!this.loadMask) {
            this.loadMask = new Ext.LoadMask(this.getEl(), {msg: msg});
        }
        this.loadMask.msg = msg;
        this.loadMask.show();
    },

    removeLoadmask: function() {
        if (this.loadMask) {
            this.loadMask.hide();
        }
    },

    getFileView: function() {
        return this.getComponent(0);
    },

    getFileWrap: function() {
        return this.getComponent(1);
    },

    getMetaGrid: function() {
        return this.getFileWrap().getComponent(0);
    },

    loadFile: function () {
        var file = this.uploadChecker.getCurrentFile(),
            data = [];

        if (!file) {
            this.close();
            return;
        }

        data.push({
            id: file.get('new_id'),
            name: file.get('new_name'),
            type: file.get('new_type'),
            size: file.get('new_size'),
            hash: file.get('new_hash'),
            src: Phlexible.Router.generate('mediamanager_upload_preview', {key: file.get('temp_key'), id: file.get('temp_id'), template: '_mm_medium'}),
            source: this.strings.wizard.new_file
        });

        if (file.get('old_id')) {
            data.push({
                id: file.get('old_id'),
                name: file.get('old_name'),
                type: file.get('old_type'),
                size: file.get('old_size'),
                hash: file.get('old_hash'),
                src: Phlexible.Router.generate('mediamanager_media', {file_id: file.get('old_id'), file_version: file.get('old_version') || 1, template_key: '_mm_medium'}),
                source: this.strings.wizard.old_file
            });
        }

        this.getFileView().getStore().loadData(data);

        if (file.get('new_metasets')) {
            var list = [];
            Ext.each(file.get('new_metasets'), function(set) {
                list.push({id: set.id, title: set.name});
            });

            this.getMetaGrid().metasetUrls.list = function() {
                return list;
            };
            this.getMetaGrid().loadMeta({
                meta: file.get('new_metasets')
            });
        } else {
            this.getMetaGrid().empty();
        }

        this.meta = {};
        if (file.get('parsed')) {
            this.meta = file.get('parsed');
        }

        var bbar = this.getBottomToolbar();
        if (file.get('old_id')) {
            bbar.items.items[4].show();
            bbar.items.items[4].setText(String.format(this.strings.wizard.save_as, file.get('alternative_name')));
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

        if (file.get('parsed') && file.get('parsed').metaset) {
            //values.metaset = file.get('parsed').metaset;
        }

        if (file.get('parsed') && file.get('parsed').metaset) {
            //this.buildMeta(file.get('parsed').metaset);
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
        var values = {},
            meta = this.getMetaGrid().getData();

        values.filename = 'xx';//this.uploadChecker.getCurrentFile().get

        if (meta) {
            values.meta = Ext.encode(meta);
        }

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
